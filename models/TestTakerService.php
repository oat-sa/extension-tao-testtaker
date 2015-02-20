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

use oat\taoTestTaker\actions\form\TestTaker as TestTakerForm;

/**
 * Service methods to manage the Subjects business models using the RDF API.
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 *
 *
 */
class TestTakerService extends \tao_models_classes_ClassService
{

    protected $subjectClass = null;

    public function __construct()
    {
        parent::__construct();
        $this->subjectClass = new \core_kernel_classes_Class(TAO_SUBJECT_CLASS);
    }

    public function getRootClass()
    {
        return $this->subjectClass;
    }

    /**
     * Short description of method getSubjectClasses
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return core_kernel_classes_ContainerCollection
     */
    public function getSubjectClasses()
    {
        return $this->subjectClass->getSubClasses(true);
    }

    /**
     * get a subject subclass by uri.
     *
     * If the uri is not set, it returns the subject class (the top level class.
     * If the uri don't reference a subject subclass, it returns null
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param string uri
     * @return core_kernel_classes_Class
     * @deprecated
     *
     */
    public function getSubjectClass($uri = '')
    {
        $returnValue = null;

        if (empty($uri) && ! is_null($this->subjectClass)) {
            $returnValue = $this->subjectClass;
        } else {
            $clazz = new \core_kernel_classes_Class($uri);
            if ($this->isSubjectClass($clazz)) {
                $returnValue = $clazz;
            }
        }

        return $returnValue;
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
            $returnValue = $subject->delete();
        }

        return (bool) $returnValue;
    }

    /**
     *
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function deleteAll()
    {
        foreach ($this->getRootClass()->getInstances(true) as $resource) {
            $resource->delete();
        }
    }

    /**
     * delete a subject class or sublcass
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param \core_kernel_classes_Class $clazz
     * @return boolean
     */
    public function deleteSubjectClass(\core_kernel_classes_Class $clazz)
    {
        $returnValue = (bool) false;

        if (! is_null($clazz)) {
            if ($this->isSubjectClass($clazz) && $clazz->getUri() != $this->subjectClass->getUri()) {
                $returnValue = $clazz->delete();
            }
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
        $roleProperty = new \core_kernel_classes_Property(PROPERTY_USER_ROLES);
        $subjectRole = new \core_kernel_classes_Resource(INSTANCE_ROLE_DELIVERY);
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
        if (!$this->isValid($instance, $clazz)) {
            throw new \common_exception_Error(__('The source Test takers data is not filled in correctly.'));
        }
        
        $returnValue = null;

        $returnValue = parent::cloneInstance($instance, $clazz);
        $userService = \tao_models_classes_UserService::singleton();
        $loginProperty = new \core_kernel_classes_Property(PROPERTY_USER_LOGIN);
        try {
            $login = $returnValue->getUniquePropertyValue($loginProperty);
            while ($userService->loginExists($login)) {
                $login .= (string) rand(0, 9);
            }

            $returnValue->editPropertyValues($loginProperty, $login);
        } catch (common_Exception $ce) {
            // empty
        }

        return $returnValue;
    }
    
    /**
     * Function checks whether the existing user is valid (all fields are filled correctly).
     * Validation based on rules discribed in the {@link tao_actions_form_Users} class
     * 
     * @param \core_kernel_classes_Resource $subject
     * @param \core_kernel_classes_Class $clazz
     * @return boolean whether the user data is valid.
     */
    public function isValid(\core_kernel_classes_Resource $subject, \core_kernel_classes_Class $clazz = null)
    {
        $valid = true;
        
        if ($clazz === null) {
            $clazz = $this->getSubjectClass();
        }
        
        $myFormContainer = new TestTakerForm($clazz, $subject, false);
        $myForm = $myFormContainer->getForm();
        
        $elemets = $myForm->getElements();
        
        foreach($elemets as $element){
            if(!$element->validate()){
                $valid = false;
                break;
            }
        }
        
        return $valid;
    }
    
}
