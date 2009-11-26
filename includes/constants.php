<?php
$todefine = array(
	'TAO_SUBJECT_CLASS' 	=> 'http://www.tao.lu/Ontologies/TAOSubject.rdf#Subject',
	'TAO_SUBJECT_NAMESPACE' => 'http://www.tao.lu/Ontologies/TAOSubject.rdf',
	'LOCAL_NAMESPACE' 		=> 'http://127.0.0.1/middleware/demoSubjects.rdf',
	'TAO_OBJECT_CLASS'		=> 'http://www.tao.lu/Ontologies/TAO.rdf#TAOObject'
);
foreach($todefine as $constName => $constValue){
	if(!defined($constName)){
		define($constName, $constValue);
	}
}
unset($todefine);
?>