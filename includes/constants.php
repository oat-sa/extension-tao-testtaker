<?php
/**
 *
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 *
 */

include_once ROOT_PATH . '/tao/includes/constants.php';

$todefine = array(
	'TAO_SUBJECT_NAMESPACE' => 'http://www.tao.lu/Ontologies/TAOSubject.rdf',
	'TAO_GROUP_MEMBERS_PROP'=> 'http://www.tao.lu/Ontologies/TAOGroup.rdf#Members'
);
foreach($todefine as $constName => $constValue){
	if(!defined($constName)){
		define($constName, $constValue);
	}
}
unset($todefine);
?>