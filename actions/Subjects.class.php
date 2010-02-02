<?php
require_once('tao/actions/CommonModule.class.php');
require_once('tao/actions/TaoModule.class.php');

/**
 * Subjects Controller provide actions performed from url resolution
 * 
 * @author Bertrand Chevrier, <taosupport@tudor.lu>
 * @package taoSubjects
 * @subpackage actions
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * 
 */

class Subjects extends TaoModule {

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
	private function getCurrentSubject(){
		
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
	
/*
 * controller actions
 */

	/**
	 * main action
	 * @return void
	 */
	public function index(){
		
		if($this->getData('reload') == true){
			unset($_SESSION[SESSION_NAMESPACE]['uri']);
			unset($_SESSION[SESSION_NAMESPACE]['classUri']);
		}
		$this->setView('index.tpl');
	}
	
	/**
	 * Render json data to populate the subject tree 
	 * 'modelType' must be in request parameter
	 * @return void
	 */
	public function getSubjects(){
		
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		$filter = '';
		if($this->hasRequestParameter('filter')){
			$filter = $this->getRequestParameter('filter');
		}
		echo json_encode($this->service->toTree( $this->service->getSubjectClass(), true, true, '', $filter));
	}
	
	/**
	 * Add an subject instance
	 */
	public function addSubject(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		$clazz = $this->getCurrentClass();
		$subject = $this->service->createInstance($clazz);
		if(!is_null($subject) && $subject instanceof core_kernel_classes_Resource){
			echo json_encode(array(
				'label'	=> $subject->getLabel(),
				'uri' 	=> tao_helpers_Uri::encode($subject->uriResource)
			));
		}
	}
	
	/**
	 * edit an subject instance
	 */
	public function editSubject(){
		$clazz = $this->getCurrentClass();
		$subject = $this->getCurrentSubject();
		$myForm = tao_helpers_form_GenerisFormFactory::instanceEditor($clazz, $subject);
		if($myForm->isSubmited()){
			if($myForm->isValid()){
				
				$subject = $this->service->bindProperties($subject, $myForm->getValues());
				
				$this->setSessionAttribute("showNodeUri", tao_helpers_Uri::encode($subject->uriResource));
				$this->setData('message', 'Subject saved');
				$this->setData('reload', true);
				$this->forward('Subjects', 'index');
			}
		}
		
		$subjectGroups = $this->service->getSubjectGroups($subject);
		$subjectGroups = array_map("tao_helpers_Uri::encode", $subjectGroups);
		$this->setData('subjectGroups', json_encode($subjectGroups));
		
		$this->setData('formTitle', 'Edit subject');
		$this->setData('myForm', $myForm->render());
		$this->setView('form_group.tpl');
	}
	
	/**
	 * add a subject model (subclass Subject)
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
	 */
	public function editSubjectClass(){
		$clazz = $this->getCurrentClass();
		$myForm = $this->editClass($clazz, $this->service->getSubjectClass());
		if($myForm->isSubmited()){
			if($myForm->isValid()){
				if($clazz instanceof core_kernel_classes_Resource){
					$this->setSessionAttribute("showNodeUri", tao_helpers_Uri::encode($clazz->uriResource));
				}
				$this->setData('message', 'class saved');
				$this->setData('reload', true);
				$this->forward('Subjects', 'index');
			}
		}
		$this->setData('formTitle', 'Edit subject class');
		$this->setData('myForm', $myForm->render());
		$this->setView('form.tpl');
	}
	
	/**
	 * delete a subject or a subject model
	 * called via ajax
	 */
	public function delete(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		
		$deleted = false;
		if($this->getRequestParameter('uri')){
			$deleted = $this->service->deleteSubject($this->getCurrentSubject());
		}
		else{
			$deleted = $this->service->deleteSubjectClass($this->getCurrentClass());
		}
		
		echo json_encode(array('deleted'	=> $deleted));
	}
	
	/**
	 * duplicate a subject instance by property copy
	 * @return void
	 */
	public function cloneSubject(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		
		$clone = $this->service->cloneInstance($this->getCurrentSubject(), $this->getCurrentClass());
		if(!is_null($clone)){
			echo json_encode(array(
				'label'	=> $clone->getLabel(),
				'uri' 	=> tao_helpers_Uri::encode($clone->uriResource)
			));
		}
	}
	
	
	/**
	 * get the list of groups to populate the checkbox tree of groups to link with
	 * @return void
	 */
	public function getGroups(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		
		echo json_encode($this->service->toTree( new core_kernel_classes_Class(TAO_GROUP_CLASS), true, true, ''));
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
		$subject = $this->getCurrentSubject();
		
		if($this->service->setSubjectGroups($subject, $groups)){
			$saved = true;
		}
		echo json_encode(array('saved'	=> $saved));
	}
	
	/*
	 * @TODO implement the following actions
	 */
	
	public function getMetaData(){
		throw new Exception("Not yet implemented");
	}
	
	public function saveComment(){
		throw new Exception("Not yet implemented");
	}
	
}
?>