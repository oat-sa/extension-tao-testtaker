<?php
require_once dirname(__FILE__) . '/../../generis/common/inc.extension.php';
require_once INCLUDES_PATH.'/simpletest/autorun.php';
/**
* @constant login for the generis module you wish to connect to 
*/
define("LOGIN", "demo", true);

/**
* @constant password for the module you wish to connect to 
*/
define("PASS", "demo", true);

/**
* @constant module for the module you wish to connect to 
*/
define("MODULE", "taotrans_demo", true);

/**
 * This class enable you to test the models managment of the taoSubjects extension
 *
 * @author Bertrand Chevrier, <taosupport@tudor.lu>
 * @package taoSubjects
 * @subpackage test
 */
class SubecjtsServiceTestCase extends UnitTestCase {
	
	/**
	 * tests initialization
	 */
	public function setUp(){		
		//connection to the API 
		core_control_FrontController::connect(LOGIN, md5(PASS), MODULE);
	}
	
	/**
	 * Test the subject models management methods from the taoSubjects_models_classes_SubjectsService class
	 * @see taoSubjects_models_classes_SubjectsService
	 */
	public function testSubjectModel(){
		
		$subjectService = tao_models_classes_ServiceFactory::get('taoSubjects_models_classes_SubjectsService');
		$this->assertIsA($subjectService, 'taoSubjects_models_classes_SubjectsService');
		
		//create a custom model for the needs of the test
		$testModelClass = $subjectService->createSubjectClass(
			null,
			'aSubjectModel',
			array(
				'aProperty' => 'aValue',
				'anOtherProperty' => 'anOtherValue'
			)
		);
		
		$this->assertIsA($testModelClass, 'core_kernel_classes_Class');
		$this->assertEqual($testModelClass->getLabel(), 'aSubjectModel');
		
		//create instances of the created custom subject model
		$testModelInstance = $subjectService->createInstance($testModelClass, 'anInstance');
		$this->assertIsA( $testModelInstance, 'core_kernel_classes_Resource');
		$this->assertEqual(count($testModelClass->getProperties()), 2);
		
		$properties = array();
		$i = 0;
		foreach($testModelClass->getProperties() as $testPorperty){
			$properties[$testPorperty->uriResource] = 'aValue_' . $i;
			$i++;
		}
		$testModelInstance = $subjectService->bindProperties( $testModelInstance, $properties );

		$this->assertIsA( $testModelInstance, 'core_kernel_classes_Resource' );
		
		//check if the model is recognized as a custom model (in opposition with the default models)
		$this->assertTrue( $subjectService->isCustom($testModelClass) );
		
		
		$myCustomProperty = $subjectService->getPropertyByLabel($testModelClass, 'aProperty');
		$this->assertIsA($myCustomProperty, 'core_kernel_classes_Property');
		
		$this->assertEqual($testModelInstance->getUniquePropertyValue($myCustomProperty), 'aValue_0');
		
		//edit property
		$newProperties = array();
		$i = 0;
		foreach($testModelClass->getProperties() as $testPorperty){
			$newProperties[$testPorperty->uriResource] = 'anEditedValue_' . $i;
			$i++;
		}
		$testModelInstance = $subjectService->bindProperties( $testModelInstance, $newProperties );
		
		//clean the created resources
		$testModelInstance->delete();
		foreach($testModelClass->getProperties() as $testPorperty){
			$testPorperty->delete();
		}
		$testModelClass->delete();
	}
	
}
?>