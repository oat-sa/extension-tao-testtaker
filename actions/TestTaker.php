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
 *               2002-2008 (update and modification) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 *               2013-2014 (update and modification) Open Assessment Technologies SA
 */
namespace oat\taoTestTaker\actions;

use common_exception_BadRequest;
use oat\taoTestTaker\actions\form\Search;
use oat\taoTestTaker\actions\form\TestTaker as TestTakerForm;
use oat\taoGroups\helpers\TestTakerForm as GroupForm;
use oat\taoTestTaker\models\TestTakerService;

/**
 * Subjects Controller provide actions performed from url resolution
 *
 * @author Bertrand Chevrier, <taosupport@tudor.lu>
 * @package taoTestTaker
 * @license GPLv2 http://www.opensource.org/licenses/gpl-2.0.php
 *         
 */
class TestTaker extends \tao_actions_SaSModule
{

    /**
     * constructor: initialize the service and the default data
     *
     * @return Subjects
     */
    public function __construct()
    {
        parent::__construct();
        
        // the service is initialized by default
        $this->service = TestTakerService::singleton();
        $this->defaultData();
    }
    
    /*
     * conveniance methods
     */

    /**
     * get the class of the current subject regarding the 'classUri' request parameter
     * if the classUri is not defined try, to find the current 'classUri' functions of the 'uri' request parameter
     *
     * @throws \common_exception_InconsistentData
     * @return core_kernel_classes_Class
     */
    protected function getCurrentClass()
    {
        $clazz = null;
        
        try {
            $clazz = parent::getCurrentClass();
        } catch (Exception $e) {
            $uri = \tao_helpers_Uri::decode($this->getRequestParameter('uri'));
            $resource = new \core_kernel_classes_Resource($uri);
            foreach ($resource->getTypes() as $type) {
                $clazz = $type;
                break;
            }
        }
        
        if (is_null($clazz)) {
            throw new \common_exception_InconsistentData("No valid class uri found");
        }
        
        return $clazz;
    }

    /**
     * get the main class
     * 
     * @return core_kernel_classes_Classes
     */
    protected function getClassService()
    {
        return $this->service;
    }

    /**
     *
     * @author Lionel Lecaque, lionel@taotesting.com
     * @param core_kernel_classes_Class $clazz            
     * @return tao_actions_form_Search
     */
    protected function getSearchForm($clazz)
    {
        return new Search($clazz, null, array(
            'recursive' => true
        ));
    }

    /**
     * edit an subject instance
     * 
     */
    public function editSubject()
    {
        $clazz = $this->getCurrentClass();
        
        // get the subject to edit
        $subject = $this->getCurrentInstance();
        
        $addMode = false;
        $login = (string) $subject->getOnePropertyValue(new \core_kernel_classes_Property(PROPERTY_USER_LOGIN));
        if (empty($login)) {
            $addMode = true;
            $this->setData('loginUri', \tao_helpers_Uri::encode(PROPERTY_USER_LOGIN));
        }
        if ($this->hasRequestParameter('reload')) {
            $this->setData('reload', true);
        }
        
        $myFormContainer = new TestTakerForm($clazz, $subject, $addMode, false);
        $myForm = $myFormContainer->getForm();
        
        if ($myForm->isSubmited()) {
            if ($myForm->isValid()) {
                $this->setData('reload', false);
                
                $values = $myForm->getValues();
                
                if ($addMode) {
                    $values[PROPERTY_USER_PASSWORD] = \core_kernel_users_Service::getPasswordHash()->encrypt($values['password1']);
                    unset($values['password1']);
                    unset($values['password2']);
                } else {
                    if (! empty($values['password2'])) {
                        $values[PROPERTY_USER_PASSWORD] = \core_kernel_users_Service::getPasswordHash()->encrypt($values['password2']);
                    }
                    unset($values['password2']);
                    unset($values['password3']);
                }
                
                $binder = new \tao_models_classes_dataBinding_GenerisFormDataBinder($subject);
                $subject = $binder->bind($values);
                
                if ($addMode) {
                    // force default subject roles to be the Delivery Role:
                    $this->service->setTestTakerRole($subject);
                }
                
                // force the data language to be the same as the gui language
                $userService = \tao_models_classes_UserService::singleton();
                $lang = new \core_kernel_classes_Resource($values[PROPERTY_USER_UILG]);
                $userService->bindProperties($subject, array(
                    PROPERTY_USER_DEFLG => $lang->getUri()
                ));
                
                $message = __('Test taker saved');
                
                if ($addMode) {
                    $params = array(
                        'uri' => \tao_helpers_Uri::encode($subject->getUri()),
                        'classUri' => \tao_helpers_Uri::encode($clazz->getUri()),
                        'reload' => true,
                        'message' => $message
                    );
                    $this->redirect(_url('editSubject', null, null, $params));
                }
                
                $this->setData("selectNode", \tao_helpers_Uri::encode($subject->getUri()));
                $this->setData('message', $message);
                $this->setData('reload', true);
            }
        }
        
        if (\common_ext_ExtensionsManager::singleton()->isEnabled('taoGroups')) {
            $this->setData('groupForm', GroupForm::renderGroupTreeForm($subject));
        }
        
        $this->setData('checkLogin', $addMode);
        $this->setData('formTitle', __('Edit subject'));
        $this->setData('myForm', $myForm->render());
        $this->setView('form_subjects.tpl');
    }

    /**
     * Edit a subject model (edit a class)
     * 
     * Override because of topclass
     */
    public function editClassProperties()
    {
        $clazz = $this->getCurrentClass();
        
        if ($this->hasRequestParameter('property_mode')) {
            $this->setSessionAttribute('property_mode', $this->getRequestParameter('property_mode'));
        }
        
        $myForm = $this->getClassForm($clazz, $this->service->getRootClass(), new \core_kernel_classes_Class('http://www.tao.lu/Ontologies/generis.rdf#User'));
        if ($myForm->isSubmited()) {
            if ($myForm->isValid()) {
                if ($clazz instanceof core_kernel_classes_Resource) {
                    $this->setData("selectNode", \tao_helpers_Uri::encode($clazz->getUri()));
                }
                $this->setData('message', __('Class saved'));
                $this->setData('reload', true);
            }
        }
        $this->setData('formTitle', __('Edit subject class'));
        $this->setData('myForm', $myForm->render());
        $this->setView('form.tpl', 'tao');
    }

    /**
     * delete a subject or a subject model called via ajax
     */
    public function delete()
    {
        if (! \tao_helpers_Request::isAjax()) {
            throw new common_exception_BadRequest("wrong request mode");
        }
        
        if ($this->getRequestParameter('uri')) {
            $deleted = $this->service->deleteSubject($this->getCurrentInstance());
        } else {
            $deleted = $this->service->deleteSubjectClass($this->getCurrentClass());
        }
        
        echo json_encode(array(
            'deleted' => $deleted
        ));
    }
    
    /**
     * Action checks whether the existing user is valid (all fields are filled correctly).
     * Requires $_REQUEST['uri'] (taoTaker uri).
     * 
     * @throws Exception
     * @see TestTakerService::isValid()
     */
    public function isValid()
    {
        $clazz = $this->getCurrentClass();
        $subject = $this->getCurrentInstance();
        
        $valid = $this->service->isValid($subject, $clazz);
        
        echo json_encode($valid);
    }
}
