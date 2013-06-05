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
	public function put($uri = null){
	}
	public function post($uri = null) {

	}
	
}
?>
