<?php
require_once dirname(__FILE__) . '/../../tao/test/TestRunner.php';
require_once dirname(__FILE__) . '/../includes/common.php';

/**
 *
 * @author Bertrand Chevrier, <taosupport@tudor.lu>
 * @package taoSubjects
 * @subpackage test
 */
class SubjectsTestCase extends UnitTestCase {
	
	/**
	 * 
	 * @var taoSubjects_models_classes_SubjectsService
	 */
	protected $subjectsService = null;
	
	/**
	 * tests initialization
	 */
	public function setUp(){		
		TestRunner::initTest();
	}
	
	/**
	 * Test the user service implementation
	 * @see tao_models_classes_ServiceFactory::get
	 * @see taoSubjects_models_classes_SubjectsService::__construct
	 */
	public function testService(){
		
		$subjectsService = tao_models_classes_ServiceFactory::get('Subjects');
		$this->assertIsA($subjectsService, 'tao_models_classes_Service');
		$this->assertIsA($subjectsService, 'taoSubjects_models_classes_SubjectsService');
		
		$this->subjectsService = $subjectsService;
	}
	
}
?>