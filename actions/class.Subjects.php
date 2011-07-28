<?php

/**
 * Subjects Controller provide actions performed from url resolution
 * 
 * @author Bertrand Chevrier, <taosupport@tudor.lu>
 * @package taoSubjects
 * @subpackage actions
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * 
 */

class taoSubjects_actions_Subjects extends tao_actions_TaoModule {

	/**
	 * constructor: initialize the service and the default data
	 * @return Subjects
	 */
	public function __construct(){
		
		parent::__construct();
		
		//the service is initialized by default
		$this->service = tao_models_classes_ServiceFactory::get('Subjects');
		$this->defaultData();
	}
	
/*
 * conveniance methods
 */
	
	/**
	 * get the instancee of the current subject regarding the 'uri' and 'classUri' request parameters
	 * @return core_kernel_classes_Resource the subject instance
	 */
	protected function getCurrentInstance(){
		
		$uri = tao_helpers_Uri::decode($this->getRequestParameter('uri'));
		if(is_null($uri) || empty($uri)){
			throw new Exception("No valid uri found");
		}
		
		$clazz = $this->getCurrentClass();
		
		$subject = $this->service->getSubject($uri, 'uri', $clazz);
		if(is_null($subject)){
			throw new Exception("No subject found for the uri {$uri}");
		}
		
		return $subject;
	}
	
	/**
	 * get the main class
	 * @return core_kernel_classes_Classes
	 */
	protected function getRootClass(){
		return $this->service->getSubjectClass();
	}
	
/*
 * controller actions
 */

	
	/**
	 * edit an subject instance
	 * @return void
	 */
	public function editSubject(){
		$clazz = $this->getCurrentClass();
		$subject = $this->getCurrentInstance();
		
		$addMode = false;
		$login = (string)$subject->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_USER_LOGIN));
		if(empty($login)){
			$addMode = true;
			$this->setData('loginUri', tao_helpers_Uri::encode(PROPERTY_USER_LOGIN));
		}
		if($this->hasRequestParameter('reload')){
			$this->setData('reload', true);
		}
		
		$myFormContainer = new tao_actions_form_Users($clazz, $subject, $addMode);
		$myForm = $myFormContainer->getForm();
		$myForm->removeElement(tao_helpers_Uri::encode(PROPERTY_USER_DEFLG));
		if(!$addMode){
			$myForm->removeElement('password0');
			$myForm->removeElement('password1');
		}
                
		if($myForm->isSubmited()){
			if($myForm->isValid()){
				$this->setData('reload', false);
				
				$values = $myForm->getValues();
				
				if($addMode){
					$values[PROPERTY_USER_PASSWORD] = md5($values['password1']);
					unset($values['password1']);
					unset($values['password2']);
				}
				else{
					if(!empty($values['password2'])){
						$values[PROPERTY_USER_PASSWORD] = md5($values['password2']);
					}
                                        //password0 and password1 have already been removed
					unset($values['password2']);
					unset($values['password3']);
				}
				
				if(!preg_match("/[A-Z]{2,4}$/", trim($values[PROPERTY_USER_UILG]))){
					unset($values[PROPERTY_USER_UILG]);
				}
				
				$subject = $this->service->bindProperties($subject, $values);
				
				if($addMode){
					//force default subject lg to the default system's:
					$userService = tao_models_classes_ServiceFactory::get('tao_models_classes_UserService');
					$lang = tao_helpers_I18n::getLangResourceByCode(DEFAULT_LANG);
					$userService->saveUser($subject, array(PROPERTY_USER_DEFLG => $lang->uriResource));
				}
                                
				$message = __('Test taker saved');
				
				if($addMode){
					$params =  array(
						'uri' 		=> tao_helpers_Uri::encode($subject->uriResource),
						'classUri' 	=> tao_helpers_Uri::encode($clazz->uriResource),
						'reload'	=> true,
						'message'	=> $message
					);
					$this->redirect(_url('editSubject', null, null, $params));
				}
				
				$this->setData('message', $message);
				$this->setData('reload',  true);
				
			}
		}
		$this->setSessionAttribute("showNodeUri", tao_helpers_Uri::encode($subject->uriResource));
		
		$this->setData('subjectGroups', json_encode(array_map("tao_helpers_Uri::encode", $this->service->getSubjectGroups($subject))));
		
		$this->setData('checkLogin', $addMode);
		$this->setData('formTitle', __('Edit subject'));
		$this->setData('myForm', $myForm->render());
		$this->setView('form_subjects.tpl');
	}
	
	/**
	 * add a subject model (subclass Subject)
	 * @return void
	 */
	public function addSubjectClass(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		$clazz = $this->service->createSubjectClass($this->getCurrentClass());
		if(!is_null($clazz) && $clazz instanceof core_kernel_classes_Class){
			echo json_encode(array(
				'label'	=> $clazz->getLabel(),
				'uri' 	=> tao_helpers_Uri::encode($clazz->uriResource)
			));
		}
	}
	
	/**
	 * Edit a subject model (edit a class)
	 * @return void
	 */
	public function editSubjectClass(){
		$clazz = $this->getCurrentClass();
		
		if($this->hasRequestParameter('property_mode')){
			$this->setSessionAttribute('property_mode', $this->getRequestParameter('property_mode'));
		}
		
		$myForm = $this->editClass($clazz, $this->service->getSubjectClass(), new core_kernel_classes_Class('http://www.tao.lu/Ontologies/generis.rdf#User'));
		if($myForm->isSubmited()){
			if($myForm->isValid()){
				if($clazz instanceof core_kernel_classes_Resource){
					$this->setSessionAttribute("showNodeUri", tao_helpers_Uri::encode($clazz->uriResource));
				}
				$this->setData('message', __('Class saved'));
				$this->setData('reload', true);
			}
		}
		$this->setData('formTitle', __('Edit subject class'));
		$this->setData('myForm', $myForm->render());
		$this->setView('form.tpl', true);
	}
	
	/**
	 * delete a subject or a subject model
	 * called via ajax
	 * @return void
	 */
	public function delete(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		
		$deleted = false;
		if($this->getRequestParameter('uri')){
			$deleted = $this->service->deleteSubject($this->getCurrentInstance());
		}
		else{
			$deleted = $this->service->deleteSubjectClass($this->getCurrentClass());
		}
		
		echo json_encode(array('deleted'	=> $deleted));
	}
	
	/**
	 * get the list of groups to populate the checkbox tree of groups to link with
	 * @return void
	 */
	public function getGroups(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		$options = array('chunk' => false);
		if($this->hasRequestParameter('classUri')) {
			$clazz = $this->getCurrentClass();
			$options['chunk'] = true;
		}
		else{
			$clazz = new core_kernel_classes_Class(TAO_GROUP_CLASS);
		}
		if($this->hasRequestParameter('selected')){
			$selected = $this->getRequestParameter('selected');
			if(!is_array($selected)){
				$selected = array($selected);
			}
			$options['browse'] = $selected;
		}
		if($this->hasRequestParameter('offset')){
			$options['offset'] = $this->getRequestParameter('offset');
		}
		if($this->hasRequestParameter('limit')){
			$options['limit'] = $this->getRequestParameter('limit');
		}
		if($this->hasRequestParameter('subclasses')){
			$options['subclasses'] = $this->getRequestParameter('subclasses');
		}
		echo json_encode($this->service->toTree($clazz, $options));
	}
	
	/**
	 * save from the checkbox tree the groups to link with 
	 * @return void
	 */
	public function saveGroups(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		$saved = false;
		$groups = array();
		foreach($this->getRequestParameters() as $key => $value){
			if(preg_match("/^instance_/", $key)){
				array_push($groups, tao_helpers_Uri::decode($value));
			}
		}
		$subject = $this->getCurrentInstance();
		
		if($this->service->setSubjectGroups($subject, $groups)){
			$saved = true;
		}
		echo json_encode(array('saved'	=> $saved));
	}
	
	
	
}
?>