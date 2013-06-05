<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 *
 * @author plichart
 */
class taoSubjects_actions_RESTSubjects extends tao_actions_CommonRESTModule {

	public function __construct(){
		parent::__construct();
		$this->service = taoSubjects_models_classes_SubjectsService::singleton();
		$this->defaultData();
	}
	public function get($uri = null){
		try {
		$data = $this->service->getTestTaker($uri);
		} catch (Exception $e) {
		    return $this->returnFailure($e->getCode(),$e->getMessage());
		}
		return $this->returnSuccess($data);
	}
	public function delete($uri = null){
		try {
		$data = $this->service->deleteTestTaker($uri);
		} catch (Exception $e) {
		    return $this->returnFailure($e->getCode(),$e->getMessage());
		}
		return $this->returnSuccess($data);
	}

	public function post() {
		try {
		$parameters = $this->getTestTakerParameters();
		$data = $this->service->createTestTaker($parameters);
		} catch (Exception $e) {
		    return $this->returnFailure($e->getCode(),$e->getMessage());
		}
		return $this->returnSuccess($data);
	}
	public function put($uri = null){

	}
	
	/**
	 * Retrieve HTTP parameters specifically for test takers (beyon user defined properties) and raise exception if there are missing parameters
	 */
	private function getTestTakerParameters() {
		$defaultParameters = parent::getDefaultParameters();
		$testTakerParameters = array(
		    //mandatory or optionnal
		    "login"=> array(PROPERTY_USER_LOGIN,true),
		    "password" => array(PROPERTY_USER_PASSWORD,true),
		    "guiLg" => array(PROPERTY_USER_UILG, false),
		    "dataLg" => array(PROPERTY_USER_DEFLG, false),
		    "firstName"=>array(PROPERTY_USER_LASTNAME,false),
		    "mail"=>array(PROPERTY_USER_MAIL,false),
		    "type"=>array(RDF_TYPE,false)   //a default type would be set
		);
		return array_merge($defaultParameters, $this->getAvailableParameters($testTakerParameters));

	}
}
?>
