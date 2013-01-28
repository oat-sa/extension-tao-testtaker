<?php

/**
 * Extends the common Import class to update the behavior
 * 
 * @author Bertrand Chevrier, <taosupport@tudor.lu>
 * @package taoSubjects
 * @subpackage actions
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * 
 */

class taoSubjects_actions_SubjectsImport extends tao_actions_Import {

	
	protected $excludedProperties = array(PROPERTY_USER_DEFLG);
	protected $additionalAdapterOptions = array();
	
	public function __construct(){
		
		parent::__construct();
		
		//Add static data to each imported subjects, here we add the subject role as 2nd Type plus the mendatory system default language
		$lang = '';
		$langResource = tao_helpers_I18n::getLangResourceByCode(DEFAULT_LANG);
		if($langResource instanceof core_kernel_classes_Resource){
			$lang = $langResource->uriResource;
		}else{
			throw new Exception('cannot find the default system language during subjects import');
		}
		
		$this->staticData = array(
			PROPERTY_USER_DEFLG => $lang,
			PROPERTY_USER_ROLES => INSTANCE_ROLE_DELIVERY
		);
		
		$this->additionalAdapterOptions = array(
			'callbacks' => array(
				'*' => array('trim'),
				PROPERTY_USER_PASSWORD => array('md5')
			)
		);
		
	}
	
}
?>