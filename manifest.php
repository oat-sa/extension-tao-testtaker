<?php
/*
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 *
 */
$extpath = dirname(__FILE__).DIRECTORY_SEPARATOR;
$taopath = dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'tao'.DIRECTORY_SEPARATOR;

return array(
	'name' => 'taoSubjects',
	'description' => 'TAO Subjects extension',
	'version' => '2.4',
	'author' => 'Open Assessment Technologies, CRP Henri Tudor',
	'dependencies' => array('tao'),
	'models' => array('http://www.tao.lu/Ontologies/TAOSubject.rdf',
		'http://www.tao.lu/Ontologies/taoFuncACL.rdf'),
	'install' => array(
		'rdf' => array(
				array('ns' => 'http://www.tao.lu/Ontologies/TAOSubject.rdf', 'file' => dirname(__FILE__). '/models/ontology/taosubject.rdf')
		),
		'checks' => array(
			array('type' => 'CheckFileSystemComponent', 'value' => array('id' => 'fs_taoSubjects_includes', 'location' => 'taoSubjects/includes', 'rights' => 'r'))
		)
	),
	'managementRole' => 'http://www.tao.lu/Ontologies/TAOSubject.rdf#SubjectsManagerRole',
	'classLoaderPackages' => array(
		dirname(__FILE__).'/actions/',
		dirname(__FILE__).'/helpers/'
	 ),
	 'constants' => array(
		# actions directory
		"DIR_ACTIONS"			=> $extpath."actions".DIRECTORY_SEPARATOR,
	
		# models directory
		"DIR_MODELS"			=> $extpath."models".DIRECTORY_SEPARATOR,
	
		# views directory
		"DIR_VIEWS"				=> $extpath."views".DIRECTORY_SEPARATOR,
	
		# helpers directory
		"DIR_HELPERS"			=> $extpath."helpers".DIRECTORY_SEPARATOR,
	
		# default module name
		'DEFAULT_MODULE_NAME'	=> 'Subjects',
	
		#default action name
		'DEFAULT_ACTION_NAME'	=> 'index',
	
		#BASE PATH: the root path in the file system (usually the document root)
		'BASE_PATH'				=> $extpath,
	
		#BASE URL (usually the domain root)
		'BASE_URL'				=> ROOT_URL	.'taoSubjects/',
	
		#BASE WWW the web resources path
		'BASE_WWW'				=> ROOT_URL	.'taoSubjects/views/',
	 
	 	#TAO extension Paths
		'TAOBASE_WWW'			=> ROOT_URL	.'tao/views/',
		'TAOVIEW_PATH'			=> $taopath.'views'.DIRECTORY_SEPARATOR,
		'TAO_TPL_PATH'			=> $taopath.'views'.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR,
	 
	)
);
?>