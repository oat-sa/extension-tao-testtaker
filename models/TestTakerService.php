<?php
/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 *               2013-2014 (update and modification) Open Assessment Technologies SA
 */
namespace oat\taoTestTaker\models;

use core_kernel_classes_Class;
use core_kernel_classes_Resource;
use oat\generis\model\GenerisRdf;
use oat\oatbox\event\EventManagerAwareTrait;
use oat\tao\model\TaoOntology;
use oat\taoTestTaker\models\events\TestTakerClassCreatedEvent;
use oat\taoTestTaker\models\events\TestTakerClassRemovedEvent;
use oat\taoTestTaker\models\events\TestTakerCreatedEvent;
use oat\taoTestTaker\models\events\TestTakerRemovedEvent;

/**
 * Service methods to manage the Subjects business models using the RDF API.
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 */
class TestTakerService extends \tao_models_classes_ClassService
{
    use EventManagerAwareTrait;

    const CLASS_URI_SUBJECT = 'http://www.tao.lu/Ontologies/TAOSubject.rdf#Subject';

    const ROLE_SUBJECT_MANAGER = 'http://www.tao.lu/Ontologies/TAOSubject.rdf#SubjectsManagerRole';

    protected $subjectClass = null;

    /**
     * TestTakerService constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->subjectClass = new \core_kernel_classes_Class(TaoOntology::SUBJECT_CLASS_URI);
    }

    /**
     * @return core_kernel_classes_Class|null
     */
    public function getRootClass()
    {
        return $this->subjectClass;
    }

    /**
     * @param core_kernel_classes_Class $clazz
     * @param string $label
     * @return core_kernel_classes_Resource
     */
    public function createInstance(core_kernel_classes_Class $clazz, $label = '')
    {
        $instance = parent::createInstance($clazz, $label);

        $this->getEventManager()->trigger(new TestTakerCreatedEvent($instance->getUri()));

        return $instance;
    }

    /**
     * @param core_kernel_classes_Class $parentClazz
     * @param string $label
     * @return core_kernel_classes_Class
     */
    public function createSubClass(core_kernel_classes_Class $parentClazz, $label = '')
    {
        $subClass = parent::createSubClass($parentClazz, $label);

        $this->getEventManager()->trigger(new TestTakerClassCreatedEvent($subClass->getUri()));

        return $subClass;
    }

    /**
     * @param core_kernel_classes_Resource $resource
     * @return bool
     */
    public function deleteResource(core_kernel_classes_Resource $resource)
    {
        $this->getEventManager()->trigger(new TestTakerRemovedEvent($resource->getUri()));

        return parent::deleteResource($resource);
    }

    /**
     * @param core_kernel_classes_Class $clazz
     * @return bool
     */
    public function deleteClass(core_kernel_classes_Class $clazz)
    {
        $this->getEventManager()->trigger(new TestTakerClassRemovedEvent($clazz->getUri()));

        return parent::deleteClass($clazz);
    }

    /**
     * delete a subject instance
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param \core_kernel_classes_Resource $subject
     * @return boolean
     */
    public function deleteSubject(\core_kernel_classes_Resource $subject)
    {
        $returnValue = (bool) false;

        if (! is_null($subject)) {
            $this->getEventManager()->trigger(new TestTakerRemovedEvent($subject->getUri()));
            $returnValue = $subject->delete();
        }

        return (bool) $returnValue;
    }

    /**
     * Check if the Class in parameter is a subclass of Subject
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param \core_kernel_classes_Class $clazz
     * @return boolean
     */
    public function isSubjectClass(\core_kernel_classes_Class $clazz)
    {
        $returnValue = (bool) false;

        if ($clazz->getUri() == $this->subjectClass->getUri()) {
            $returnValue = true;
        } else {
            foreach ($this->subjectClass->getSubClasses(true) as $subclass) {
                if ($clazz->getUri() == $subclass->getUri()) {
                    $returnValue = true;
                    break;
                }
            }
        }

        return (bool) $returnValue;
    }

    /**
     * Set the proper role to the testTaker
     * 
     * @author Lionel Lecaque, lionel@taotesting.com
     * @param \core_kernel_classes_Resource $instance
     */
    public function setTestTakerRole(\core_kernel_classes_Resource $instance){
        $roleProperty = new \core_kernel_classes_Property(GenerisRdf::PROPERTY_USER_ROLES);
        $subjectRole = new \core_kernel_classes_Resource(TaoOntology::PROPERTY_INSTANCE_ROLE_DELIVERY);
        $instance->setPropertyValue($roleProperty, $subjectRole);
    }

    /**
     * Short description of method cloneInstance
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param \core_kernel_classes_Resource $instance
     * @param \core_kernel_classes_Class $clazz
     * @throws \common_Exception
     * @throws \core_kernel_classes_EmptyProperty
     * @return core_kernel_classes_Resource
     */
    public function cloneInstance(\core_kernel_classes_Resource $instance, \core_kernel_classes_Class $clazz = null)
    {
        $loginProperty = new \core_kernel_classes_Property(GenerisRdf::PROPERTY_USER_LOGIN);
        $login = $instance->getUniquePropertyValue($loginProperty);
        
        $returnValue = parent::cloneInstance($instance, $clazz);
        $userService = \tao_models_classes_UserService::singleton();
        try {
            while ($userService->loginExists($login)) {
                $login .= (string) rand(0, 9);
            }

            $returnValue->editPropertyValues($loginProperty, $login);
        } catch (common_Exception $ce) {
            // empty
        }

        return $returnValue;
    }
}
