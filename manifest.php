<?php
	return array(
		'name' => 'TAO Subjects',
		'description' => 'TAO Subjects extensions http://www.tao.lu',
		'additional' => array(
			'version' => '1.0',
			'author' => 'CRP Henry Tudor',
			'dependances' => array(),
			'install' => array( 
				'sql' => dirname(__FILE__). '/model/ontology/taosubjects.sql',
				'php' => dirname(__FILE__). '/install/install.php'
			)
			

				
			
		)
	);
?>