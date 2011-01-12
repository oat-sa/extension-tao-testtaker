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

	/**
	 * Add static data to each imported subjects,
	 * here we add the subject role as 2nd Type 
	 * @var array
	 */
	protected $staticData = array(
		RDF_TYPE	=> CLASS_ROLE_SUBJECT
	);
	
}
?>