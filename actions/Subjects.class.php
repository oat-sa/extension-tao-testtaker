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
		$this->setData('currentNode', (!is_null($this->getRequestParameter('currentNode'))) ? $this->getRequestParameter('currentNode') : -1 );
		$this->setView('index.tpl');
	}
	
	/**
	 * Render json data to populate the subject tree 
	 * 'modelType' must be in request parameter
	 * @return void
	 */
	public function getSubjectModel(){
		
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		
		$data = array();
		
		//check parameters
		$type = $this->getRequestParameter('type');
		if(is_null($type) || empty($type)){
			throw new Exception("Please specify a type of model");
		}
		
		//get subject models
		foreach($this->service->getSubjectModels()->getIterator() as $model){
			
			if($this->service->isCustom($model) && $type == 'common-subject'){
				continue;
			}
			if(!$this->service->isCustom($model) && $type == 'custom-subject'){
				continue;
			}
			
			//format instances for json tree datastore 
			$instances = array();
			foreach($this->service->getSubjects($model)->getIterator() as $instance){
				$instances[] = array(
					'data' 	=> $instance->getLabel(),
					'attributes' => array(
						'id' => tao_helpers_Uri::encode($instance->uriResource),
						'class' => 'node-instance'
					)
				);
			}
			
			//format classes for json tree datastore 
			$modelData = array(
					'data' 	=> $model->getLabel(),
					'attributes' => array(
							'id' => tao_helpers_Uri::encode($model->uriResource),
							'class' => 'node-class'
						),
					'children'	=> $instances
				);
			$data[] = $modelData;
		}
		
		//render directly the json
		echo json_encode($data);
	}
	
	/**
	 * Enable to the user to add a new model (ie. an RDF Class)
	 * @return 
	 */
	public function addModel(){
		
		$myForm = tao_helpers_form_GenerisFormFactory::createFromClass( new core_kernel_classes_Class( 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Class' ) );
		if($myForm->isSubmited()){
			if($myForm->isValid()){
				
				
				$model = $this->service->createSubjectModel($myForm->getValue('http://www.w3.org/2000/01/rdf-schema#label'));			
				$this->setData('message', 'Model added');
				//$this->forward('Subjects', 'index');
			}
		}
		
		$this->setData('formTitle', 'Add a subject model');
		$this->setData('myForm', $myForm->render());
		
		$this->setView('form.tpl');
	}
	
	/**
	 * Enable to the user to edit a model (ie. an RDF Class)
	 * @return 
	 */
	public function editModel(){
		$model = $this->getCurrentModel();
		if(!$this->service->isCustom($model)){
			throw new Exception("You cannot edit a model you have not created");
		}
		
		$myForm = tao_helpers_form_GenerisFormFactory::createFromClass( $model );
		
		$this->setData('formTitle', 'Edit subject model');
		$this->setData('myForm', $myForm->render());
		$this->setView('form.tpl');
	}
	
	/**
	 * Enable to the user to remove a model
	 * @return 
	 */
	public function deleteModel(){
		$model = $this->getCurrentModel();
		if(!$this->service->isCustom($model)){
			throw new Exception("You cannot delete a model you have not created");
		}
		
		$this->forward('Subjects', 'index');
	}
	
	/**
	 * add an instance of the selected model
	 * @return 
	 */
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
	
	/**
	 * 
	 * @return 
	 */
	public function createInstance(){
		
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		
		$instance = $this->service->createInstance($this->getCurrentModel());
		echo json_encode(array(
			'label'	=> $instance->getLabel(),
			'uri' 	=> tao_helpers_Uri::encode($instance->uriResource)
		));
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
					$this->service->bindProperties($subject, $myForm->getValues());
					$this->setData('message', 'Form submited');
				}
			}
			$this->setData('formTitle', 'Edit model instance');
			$this->setData('myForm', $myForm->render());
			
			$this->setView('form.tpl');
		}
		catch(Exception $e){
			print "<pre>$e</pre>";	
		}
	}
	
	public function deleteModelInstance(){
		
		$message = "Unable to delete subject";
		try{
			$subject = $this->getCurrentSubject();
			if($this->service->isCustom($subject)){
				if($this->service->deleteSubject($subject)){
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