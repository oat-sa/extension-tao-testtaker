<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 *
 * @author plichart
 */
class taoSubjects_actions_RestSubjects extends tao_actions_CommonRestModule {

	public function __construct(){
		parent::__construct();
		$this->service = taoSubjects_models_classes_SubjectsService::singleton();
		$this->defaultData();
	}
	public function get($uri = null){
		try {
		    if (!is_null($uri)){
			if (!common_Utils::isUri($uri)){
			    throw new common_exception_InvalidArgumentType();
			}
			if (!($this->service->isInScope($uri))){
			    throw new common_exception_PreConditionFailure("The URI must be a valid resource under the root Class");
			}
			$data = $this->service->getTestTaker($uri);
		    } else {
			$data = $this->service->getAllTestTakers();
		    }
		} catch (Exception $e) {
		    return $this->returnFailure($e);
		}
		return $this->returnSuccess($data);
	}
	public function delete($uri = null){
		try {
		    if (!is_null($uri)){
			if (!common_Utils::isUri($uri)){
			    throw new common_exception_InvalidArgumentType();
			}
			if (!($this->service->isInScope($uri))){
			    throw new common_exception_PreConditionFailure("The URI must be a valid resource under the root Class");
			}
			$data = $this->service->deleteTestTaker($uri);
		    } else {
			$data = $this->service->deleteAllTestTakers();
		    }
		} catch (Exception $e) {
		    return $this->returnFailure($e);
		}
		return $this->returnSuccess($data);
	}
	public function post() { 
		try {
		$parameters = $this->getParameters();
		$data = $this->service->createTestTaker($parameters);
		} catch (Exception $e) {
		    return $this->returnFailure($e);
		}
		return $this->returnSuccess($data);
	}
	public function put($uri = null){
		try {
			if (!common_Utils::isUri($uri)){
			    throw new common_exception_InvalidArgumentType();
			}
			if (!($this->service->isInScope($uri))){
			    throw new common_exception_PreConditionFailure("The URI must be a valid resource under the root Class");
			}

			$parameters = $this->getParameters(false);
			$data = $this->service->updateTestTaker($uri, $parameters);
		} catch (Exception $e) {
			return $this->returnFailure($e);
		}
		return $this->returnSuccess($data);
	}
	/**Return all parameters default, custom and specific
	 */
	protected function getExpectedParameters() {
		$defaultParameters = parent::getExpectedParameters();
		$testTakerParameters = array(
		    //mandatory or optionnal
		    "login"=> array(PROPERTY_USER_LOGIN,true),
		    "password" => array(PROPERTY_USER_PASSWORD,true),
		    "guiLg" => array(PROPERTY_USER_UILG, false),
		    "dataLg" => array(PROPERTY_USER_DEFLG, false),
		    "firstName"=>array(PROPERTY_USER_LASTNAME,false),
		    "mail"=>array(PROPERTY_USER_MAIL,false),
		    "type"=>array(RDF_TYPE,false)   //a contextual type would be set
		);
		return array_merge($defaultParameters, $testTakerParameters);
	}
}
?>
