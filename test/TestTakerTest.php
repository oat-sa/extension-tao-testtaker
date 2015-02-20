<?php

/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 *
 */

namespace oat\taoTestTaker\test;

use oat\tao\test\TaoPhpUnitTestRunner;
use oat\taoTestTaker\models\TestTakerService;



/**
 * @author Bertrand Chevrier, <taosupport@tudor.lu>
 * @package taoTestTaker
 */
class TestTakerTest extends TaoPhpUnitTestRunner
{

    /**
     * @var TestTakerService
     */
    protected $subjectsService = null;

    /**
     * tests initialization
     *
     * @return void
     */
    public function setUp()
    {
        TaoPhpUnitTestRunner::initTest();
        $this->subjectsService = TestTakerService::singleton();
    }

    /**
     * Test the user service implementation
     * @see tao_models_classes_ServiceFactory::get
     * @see TestTakerService::__construct
     * @return void
     */
    public function testService()
    {
        $this->assertIsA($this->subjectsService, '\tao_models_classes_Service');
        $this->assertIsA($this->subjectsService, 'oat\taoTestTaker\models\TestTakerService');
    }

    /**
     * @return \core_kernel_classes_Class|null
     */
    public function testClassCreate()
    {
        $this->assertTrue(defined('TAO_SUBJECT_CLASS'));
        $subjectClass = $this->subjectsService->getRootClass();
        $this->assertIsA($subjectClass, 'core_kernel_classes_Class');
        $this->assertEquals(TAO_SUBJECT_CLASS, $subjectClass->getUri());

        $this->assertTrue($this->subjectsService->isSubjectClass($subjectClass));

        return $subjectClass;
    }

    /**
     * @depends testClassCreate
     * @param $subjectClass
     * @return \core_kernel_classes_Class
     */
    public function testSubClassCreate($subjectClass)
    {
        $subSubjectClassLabel = 'subSubject class';
        $subSubjectClass = $this->subjectsService->createSubClass($subjectClass, $subSubjectClassLabel);
        $this->assertIsA($subSubjectClass, 'core_kernel_classes_Class');
        $this->assertEquals($subSubjectClassLabel, $subSubjectClass->getLabel());

        $this->assertTrue($this->subjectsService->isSubjectClass($subSubjectClass));

        return $subSubjectClass;
    }

    /**
     * @depends testClassCreate
     * @param $class
     * @return \core_kernel_classes_Resource
     */
    public function testInstantiateClass($class)
    {
        $subjectInstanceLabel = 'subject instance';
        return $this->instantiateClass($class, $subjectInstanceLabel);
    }

    /**
     * @depends testSubClassCreate
     * @param $class
     * @return \core_kernel_classes_Resource
     */
    public function testInstantiateSubClass($class)
    {
        $subSubjectInstanceLabel = 'subSubject instance';
        return $this->instantiateClass($class, $subSubjectInstanceLabel);
    }

    /**
     * @param $class
     * @param $label
     * @return \core_kernel_classes_Resource
     */
    protected function instantiateClass($class, $label)
    {
        $instance = $this->subjectsService->createInstance($class, $label);
        $this->assertIsA($instance, 'core_kernel_classes_Resource');
        $this->assertEquals($label, $instance->getLabel());

        $this->assertTrue(defined('RDFS_LABEL'));
        $instance->removePropertyValues(new \core_kernel_classes_Property(RDFS_LABEL));
        $instance->setLabel($label);


        $this->assertIsA($instance, 'core_kernel_classes_Resource');
        $this->assertEquals($label, $instance->getLabel());
        return $instance;
    }

    /**
     * 
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testGetSubjectClass()
    {
        $classes = $this->subjectsService->getSubjectClasses();
        $this->assertGreaterThanOrEqual(1, count($classes));
        $this->assertIsA(array_pop($classes), get_class($this->subjectsService->getRootClass()));
    }

    /**
     * @depends testInstantiateClass
     * @param \core_kernel_classes_Resource $instance
     */
    public function testSetTestTakerRole($instance)
    {
        $this->subjectsService->setTestTakerRole($instance);
        $propertyRoles = new \core_kernel_classes_Property(PROPERTY_USER_ROLES);

        $this->assertEquals($instance->getPropertyValues($propertyRoles)[0], INSTANCE_ROLE_DELIVERY);
    }

    /**
     * @depends testInstantiateClass
     * @param $instance
     * @expectedException \common_exception_Error
     */
    public function testFailClone($instance)
    {
        $this->subjectsService->cloneInstance($instance);
    }
    
    /**
     * @depends testInstantiateClass
     * @param $instance
     * @author Aleh Hutnikau, hutnikau@1pt.com
     */
    public function testIsValid($instance) 
    {
        $propertyLogin = new \core_kernel_classes_Property(PROPERTY_USER_LOGIN);
        $propertyName = new \core_kernel_classes_Property(PROPERTY_USER_FIRSTNAME);
        $propertyLang = new \core_kernel_classes_Property(PROPERTY_USER_UILG);
        $lang = \tao_helpers_I18n::getLangResourceByCode('en-US');
        
        $this->assertFalse($this->subjectsService->isValid($instance));
        
        $instance->setPropertyValue($propertyLogin, 'testUser_'.time(true));
        $instance->setPropertyValue($propertyName, 'testName_'.time(true));
        $instance->setPropertyValue($propertyLang, $lang->uriResource);
        
        $this->assertTrue($this->subjectsService->isValid($instance));
    }
    
    /**
     * @depends testInstantiateClass
     * @param \core_kernel_classes_Resource $instance
     */
    public function testClone($instance)
    {
        $propertyName = new \core_kernel_classes_Property(PROPERTY_USER_FIRSTNAME);
        $propertyLogin = new \core_kernel_classes_Property(PROPERTY_USER_LOGIN);

        $clone = $this->subjectsService->cloneInstance($instance);
        $this->assertEquals($instance->getPropertyValues($propertyName)[0], $clone->getPropertyValues($propertyName)[0]);
        $this->assertNotEquals($instance->getPropertyValues($propertyLogin)[0], $clone->getPropertyValues($propertyLogin)[0]);

        $this->assertNotEquals($instance, $clone);
        $this->assertTrue($this->subjectsService->deleteSubject($clone));
    }
    

    /**
     * @depends testSubClassCreate
     * @param $class
     */
    public function testDeleteClass($class)
    {
        $this->assertTrue($this->subjectsService->deleteSubjectClass($class));
        $this->assertFalse($class->exists());
    }

    /**
     * @depends testInstantiateClass
     * @param \core_kernel_classes_Resource $instance
     */
    public function testDeleteInstance($instance)
    {
        $this->assertTrue($this->subjectsService->deleteSubject($instance));
        $this->assertFalse($instance->exists());
    }
}