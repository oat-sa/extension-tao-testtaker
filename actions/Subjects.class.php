<?php
/**
 * Subjects Controller 
 */
class Subjects extends Module {

/*
 * controller actions
 */

	public function index(){
		
		$subjectService = tao_models_classes_ServiceFactory::get('Subjects');
		
		$commonModels = array();
		$customModels = array();
		
		foreach($subjectService->getSubjectModels()->getIterator() as $model){
			
			$instances = array();
			foreach($subjectService->getSubjects($model)->getIterator() as $instance){
				$instances[uniqid()] = array(
					'label' 	=> $instance->getLabel(),
					'uri'		=> tao_helpers_Uri::encode($instance->uriResource)
				);
			}
			
			$modelData = array(
				'label' 	=> $model->getLabel(),
				'uri'		=> tao_helpers_Uri::encode($model->uriResource),
				'instances'	=> $instances
			);
			
			if($subjectService->isCustom($model)){
				$customModels[uniqid()] = $modelData;
			}
			else{
				$commonModels[uniqid()] = $modelData;
			}
		}
		
		$this->setData('commonModels', $commonModels);
		$this->setData('customModels', $customModels);
		
		$this->setData('currentNode', (!is_null($this->getRequestParameter('currentNode'))) ? $this->getRequestParameter('currentNode') : -1 );
		
		$this->setView('index.tpl');
	}
	
	
	
	public function addModel(){
		
		$subjectService = tao_models_classes_ServiceFactory::get('Subjects');
		$commonProperties = array();
		foreach($subjectService->getSubjectClassProperties()->getIterator() as $propertyClass){
			$commonProperties[uniqid()] = array(
					'label' 	=> $propertyClass->getLabel(),
					'uri'		=> tao_helpers_Uri::encode($propertyClass->uriResource)
				);
		}
		$this->setData('commonProperties', $commonProperties);
		
		$myForm = tao_helpers_form_GenerisFormFactory::createFromClass( new core_kernel_classes_Class( 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property' ) );
		
		$this->setData('formTitle', 'Add a subject model');
		$this->setData('myForm', $myForm->render());
		
		$this->setView('form.tpl');
	}
	
	public function editModel(){
		echo 'edit model';
		$this->forward('Subjects', 'index');
	}
	
	public function deleteModel(){
		echo 'model deleted';
		$this->forward('Subjects', 'index');
	}
	
	public function addModelInstance(){
		try{
			$myForm = tao_helpers_form_GenerisFormFactory::createFromClass( 
				$this->getCurrentModel()
			);
			
			$this->setData('formTitle', 'Add a model instance');
			$this->setData('myForm', $myForm->render());
			
			$this->setView('form.tpl');
		}
		catch(Exception $e){
			print $e;	
		}
	}
	
	public function editModelInstance(){
		try{
			$subject = $this->getCurrentSubject();
			$myForm = tao_helpers_form_GenerisFormFactory::createFromClass( 
				$this->getCurrentModel(),
				$subject
			);
			if($myForm->isSubmited()){
				if($myForm->isValid()){
					$subjectService = tao_models_classes_ServiceFactory::get('Subjects');
					
					$subjectService->bindProperties($subject, $myForm->getValues());
					
					$this->setData('message', 'Form submited');
				}
			}
			$this->setData('formTitle', 'Edit model instance');
			$this->setData('myForm', $myForm->render());
			
			$this->setView('form.tpl');
		}
		catch(Exception $e){
			print $e;	
		}
	}
	
	public function deleteModelInstance(){
		
		$message = "Unable to delete subject";
		try{
			$subject = $this->getCurrentSubject();
			$subjectService = tao_models_classes_ServiceFactory::get('Subjects');
			
			if($subjectService->isCustom($subject)){
				if($subjectService->deleteSubject($subject)){
					$message = "Subject deleted successfully!";
				}
			}
			else{
				$message .=  " which has not been created from this interface." ;
			}
			
		}
		catch(Exception $e){
			$message .= ". {$e->getMessage()}";
		}
		$this->setData('message', $message);
		$this->forward('Subjects', 'index');
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
		
		$subjectService = tao_models_classes_ServiceFactory::get('Subjects');
		
		$model = $subjectService->getSubjectModel($classUri);
		$subject = $subjectService->getSubject($uri, 'uri', $model);
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
		
		$subjectService = tao_models_classes_ServiceFactory::get('Subjects');
		
		$model = $subjectService->getSubjectModel($classUri);
		if(is_null($model)){
			throw new Exception("No class found for the uri {$classUri}");
		}
		
		return $model;
	}
}
?>