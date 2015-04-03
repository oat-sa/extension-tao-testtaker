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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA
 * @author Mikhail Kamarouski, kamarouski@1pt.com
 */
namespace oat\taoTestTaker\models;

use core_kernel_classes_Class;
use core_kernel_classes_Resource;
use oat\taoGroups\models\GroupsService;
use oat\taoTestTaker\actions\form\InformForm;

/**
 *
 * Crud services implements basic CRUD services, orginally intended for
 * REST controllers/ HTTP exception handlers.
 * Consequently the signatures and behaviors is closer to REST and throwing
 * HTTP like exceptions.
 *
 * @author Mikhail Kamarouski, kamarouski@1pt.com
 *
 */
class InformationService extends CrudService
{
    const PRINT_SEPARATE_PAGES  = 'PrintSeparate';
    const PRINT_SINGLE_TABLE    = 'PrintSingleTable';
    const MAIL_NOTIFY           = 'MailNotify';

    protected function getRootClass()
    {
        return TestTakerService::singleton()->getRootClass();
    }


    /**
     * @param \core_kernel_classes_Resource $instance
     * @param array $options
     *
     * @return string password
     */
    public function resetPassword( \core_kernel_classes_Resource $instance, array $options = array() )
    {
        $password         = GeneratorService::singleton()->generatePassword(
            $options[InformForm::PWD_LENGTH],
            $options[InformForm::PWD_CONTROL]
        );
        $passwordProperty = new \core_kernel_classes_Property( PROPERTY_USER_PASSWORD );
        $instance->editPropertyValues(
            $passwordProperty,
            \core_kernel_users_Service::getPasswordHash()->encrypt( $password )
        );

        return $password;
    }


    /**
     * Served types with human readable comments
     * @return array
     */
    public function getAvailableTypes()
    {
        return array(
            InformationService::MAIL_NOTIFY          => __( 'Notify by email' ),
            InformationService::PRINT_SINGLE_TABLE   => __( 'Print single table' ),
            InformationService::PRINT_SEPARATE_PAGES => __( 'Print separate pages per testaker' )
        );
    }


    /**
     * @param array $actions
     * @param core_kernel_classes_Resource|core_kernel_classes_Class $target target testtaker/group instance/class
     * @param array $options
     *
     * @return \common_Serializable
     * @throws \Exception
     */
    public function invoke( array $actions, core_kernel_classes_Resource $target, array $options = array() )
    {

        $result     = array();
        $testTakers = $this->getTargetTestTakers( $target );

        $testTakers = array_map(
            function ( $testTaker ) use ( $options ) {
                $password       = InformationService::singleton()->resetPassword( $testTaker, $options );
                $user           = new \core_kernel_users_GenerisUser( $testTaker );
                $user->password = $password;

                return $user;
            },
            $testTakers
        );


        foreach ($actions as $action) {
            $class = __NAMESPACE__ . '\\command\\' . $action;
            if (class_exists( $class )) {
                $$action         = new $class;
                $result[$action] = $$action->invoke( $testTakers, $options );
            } else {
                throw new \Exception( 'invalid action ' . $action );
            }
        }

        return (object) $result;

    }


    /**
     * Retrieve testakers if found any related to Resource
     * @param core_kernel_classes_Resource $targetObject
     *
     * @return array
     */
    protected function getTargetTestTakers( core_kernel_classes_Resource $targetObject )
    {
        $guess1 = TestTakerService::singleton()->getTestTakers( $targetObject );
        $guess2 = GroupsService::singleton()->getTestTakers( $targetObject );

        return array_merge( $guess1, $guess2 );
    }


}
