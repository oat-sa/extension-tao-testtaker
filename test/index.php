<?php
require_once dirname(__FILE__) . '/../../tao/test/TestRunner.php';

//get the test into each extensions
$tests = TestRunner::getTests(array('taoSubjects'));

//create the test sutie
$testSuite = new TestSuite('TAO Subject unit tests');
foreach($tests as $testCase){
	$testSuite->addFile($testCase);
}    

//add the reporter regarding the context
if(PHP_SAPI == 'cli'){
	$reporter = new XmlTimeReporter();
}
else{
	$reporter = new HtmlReporter();
}
//run the unit test suite
$testSuite->run($reporter);
?>