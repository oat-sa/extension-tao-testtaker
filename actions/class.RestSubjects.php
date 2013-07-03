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
	}
	
	/**
	 * Optionnaly a specific rest controller may declare
	 * aliases for parameters used for the rest communication
	 */
	protected function getParametersAliases(){
	    return array_merge(parent::getParametersAliases(), array(
		    "login"=> PROPERTY_USER_LOGIN,
		    "password" => PROPERTY_USER_PASSWORD,
		    "guiLg" => PROPERTY_USER_UILG,
		    "dataLg" => PROPERTY_USER_DEFLG,
		    "firstName"=> PROPERTY_USER_LASTNAME,
		    "mail"=> PROPERTY_USER_MAIL,
		    "type"=> RDF_TYPE
	    ));
	}
	/**
	 * Optionnal Requirements for parameters to be sent on every service
	 *
	 */
	protected function getParametersRequirements() {
	    return array(
		/** you may use either the alias or the uri, if the parameter identifier
		 *  is set it will become mandatory for the operation in $key
		* Default Parameters Requirents are applied
		* type by default is not required and the root class type is applied
		*/
		"post"=> array("login", "password")
	    );
	}
	
}
?>
