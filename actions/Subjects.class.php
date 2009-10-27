<?php
/**
 * Subjects Controller provide actions performed from url resolution
 * 
 * @author Bertrand Chevrier, <taosupport@tudor.lu>
 * @package taoSubjects
 * @subpackage actions
 */
class Subjects extends Module {

	protected $service = null;

	public function __construct(){
		$this->service = tao_models_classes_ServiceFactory::get('Subjects');
	}
	
/*
 * controller actions
 */

	/**
	 * main action
	 * @return void
	 */
	public function index(){
		$context = Context::getInstance();
		$this->setData('content', "this is the ". get_class($this) ." module, " . $context->getActionName());
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
		$highlightUri = '';
		if($this->hasSessionAttribute("showNodeUri")){
			$highlightUri = $this->getSessionAttribute("showNodeUri");
			unset($_SESSION[SESSION_NAMESPACE]["showNodeUri"]);
		} 
		/*$data = array();
		foreach($this->service->getSubjectModels() as $model){
			$data = array_merge($data, $this->service->toTree($model, true, true, $highlightUri));
		}*/
		$data = $this->service->toTree(new core_kernel_classes_Class( TAO_SUBJECT_CLASS ), true, true, $highlightUri);
		echo json_encode($data);
	}
	
	/**
	 * Add an subject instance
	 */
	public function addInstance(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		$model = $this->getCurrentModel();
		$subject = $this->service->createInstance($model);
		if($subject instanceof core_kernel_classes_Resource){
			echo json_encode(array(
				'label'	=> $subject->getLabel(),
				'uri' 	=> tao_helpers_Uri::encode($subject->uriResource)
			));
		}
	}
	
	/**
	 * edit an subject instance
	 */
	public function editInstance(){
		$model = $this->getCurrentModel();
		$subject = $this->getCurrentSubject();
		$myForm = tao_helpers_form_GenerisFormFactory::instanceEditor($model, $subject);
		if($myForm->isSubmited()){
			if($myForm->isValid()){
				
				$this->service->bindProperties($subject, $myForm->getValues());
				
				$this->setSessionAttribute("showNodeUri", tao_helpers_Uri::encode($subject->uriResource));
				$this->setData('message', 'Subject saved');
				$this->setData('reload', true);
				$this->forward('Subjects', 'index');
			}
		}
		
		$this->setData('formTitle', 'Create a new Subject');
		$this->setData('myForm', $myForm->render());
		$this->setView('form.tpl');;
	}
	
	/**
	 * add a subject model (subclass Subject)
	 */
	public function addModel(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		$model = $this->service->createSubClass($this->getCurrentModel());
		if($model instanceof core_kernel_classes_Class){
			echo json_encode(array(
				'label'	=> $model->getLabel(),
				'uri' 	=> tao_helpers_Uri::encode($model->uriResource)
			));
		}
	}
	
	/**
	 * Edit a subject model (edit a class)
	 */
	public function editModel(){
		$myForm = tao_helpers_form_GenerisFormFactory::classEditor($this->getCurrentModel(), new core_kernel_classes_Class( TAO_SUBJECT_CLASS ));
		if($myForm->isSubmited()){
			if($myForm->isValid()){
				
				$classValues = array();
				$propertyValues = array();
				foreach($myForm->getValues() as $key => $value){
					if(preg_match("/^class_/", $key)){
						$classKey =  tao_helpers_Uri::decode(str_replace('class_', '', $key));
						$classValues[$classKey] =  tao_helpers_Uri::decode($value);
					}
					if(preg_match("/^property_/", $key)){
						$key = str_replace('property_', '', $key);
						$propNum = substr($key, 0, 1 );
						$propKey = tao_helpers_Uri::decode(str_replace($propNum.'_', '', $key));
						$propertyValues[$propNum][$propKey] = tao_helpers_Uri::decode($value);
					}
				}
				$clazz = $this->service->bindProperties($this->getCurrentModel(), $classValues);
				foreach($propertyValues as $propNum => $properties){
					$this->service->bindProperties(new core_kernel_classes_Resource(tao_helpers_Uri::decode($_POST['propertyUri'.$propNum])), $properties);
				}
				if($clazz instanceof core_kernel_classes_Resource){
					$this->setSessionAttribute("showNodeUri", tao_helpers_Uri::encode($clazz->uriResource));
				}
				$this->setData('message', 'model saved');
				$this->setData('reload', true);
				$this->forward('Subjects', 'index');
			}
		}
		
		$this->setData('formTitle', 'Edit a model');
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
			$deleted = $this->service->deleteSubjectModel($this->getCurrentModel());
		}
		echo json_encode(array('deleted'	=> $deleted));
	}
	
	/**
	 * duplicate a subject instance by property copy
	 */
	public function duplicate(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		
		$subject = $this->getCurrentSubject();
		$model = $this->getCurrentModel();
		
		$clone = $this->service->createInstance($model);
		if(!is_null($clone)){
			
			foreach($model->getProperties() as $property){
				foreach($subject->getPropertyValues($property) as $propertyValue){
					$clone->setPropertyValue($property, $propertyValue);
				}
			}
			$clone->setLabel($subject->getLabel()."'");
			echo json_encode(array(
				'label'	=> $clone->getLabel(),
				'uri' 	=> tao_helpers_Uri::encode($clone->uriResource)
			));
		}
	}
	
	public function import(){
		$context = Context::getInstance();
		$this->setData('content', "this is the ". get_class($this) ." module, " . $context->getActionName());
		$this->setView('index.tpl');
	}
	
	public function export(){
		$context = Context::getInstance();
		$this->setData('content', "this is the ". get_class($this) ." module, " . $context->getActionName());
		$this->setView('index.tpl');
	}
	

	/*
	 * conveniance methods
	 */
	private function getCurrentSubject(){
		$uri = tao_helpers_Uri::decode($this->getRequestParameter('uri'));
		$classUri = tao_helpers_Uri::decode($this->getRequestParameter('classUri'));
		if(is_null($uri) || empty($uri) || is_null($classUri) || empty($classUri)){
			throw new Exception("No valid uri found");
		}
		
		$model = $this->service->getSubjectModel($classUri);
		$subject = $this->service->getSubject($uri, 'uri', $model);
		if(is_null($subject)){
			throw new Exception("No subject found for the uri {$uri}");
		}
		
		return $subject;
	}
	
	private function getCurrentModel(){
		$classUri = tao_helpers_Uri::decode($this->getRequestParameter('classUri'));
		if(is_null($classUri) || empty($classUri)){
			throw new Exception("No valid uri found");
		}
		
		$model = $this->service->getSubjectModel($classUri);
		if(is_null($model)){
			throw new Exception("No class found for the uri {$classUri}");
		}
		
		return $model;
	}
}
?>