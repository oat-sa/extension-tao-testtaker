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
		$this->service = taoSubjects_models_classes_CrudSubjectsService::singleton();
		$this->defaultData();
	}
	
	/**Return all parameters default, custom and specific
	 */
	protected function getExpectedParameters() {
		$defaultParameters = parent::getExpectedParameters();
		$subjectParameters = array(
		    //mandatory or optionnal
		    "login"=> array(PROPERTY_USER_LOGIN,true),
		    "password" => array(PROPERTY_USER_PASSWORD,true),
		    "guiLg" => array(PROPERTY_USER_UILG, false),
		    "dataLg" => array(PROPERTY_USER_DEFLG, false),
		    "firstName"=>array(PROPERTY_USER_LASTNAME,false),
		    "mail"=>array(PROPERTY_USER_MAIL,false),
		    "type"=>array(RDF_TYPE,false)   //a contextual type would be set
		);
		return array_merge($defaultParameters, $subjectParameters);
	}
}
?>
