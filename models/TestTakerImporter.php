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
 * Copyright (c) 2018 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 */

namespace oat\taoTestTaker\models;

use oat\generis\Helper\UserHashForEncryption;
use oat\generis\model\OntologyRdf;
use oat\generis\model\user\UserRdf;
use oat\oatbox\event\EventManager;
use oat\tao\model\TaoOntology;
use oat\tao\model\user\import\RdsUserImportService;
use oat\tao\model\user\TaoRoles;
use oat\taoTestTaker\models\events\TestTakerUpdatedEvent;

/**
 * Class TestTakerImporter
 *
 * Implementation of RdsUserImportService to import test-taker resource from a CSV
 *
   `
    $userImporter = $this->getServiceLocator()->get(UserCsvImporterFactory::SERVICE_ID);
    $importer = $userImporter->getImporter(TestTakerImporter::USER_IMPORTER_TYPE);
    $report = $importer->import($filePath);
   `
 *
 * or by command line:
`
sudo -u www-data php index.php 'oat\tao\scripts\tools\import\ImportUsersCsv' -t test-taker -f tao/test/user/import/example.csv
`
 * @package oat\taoTestTaker\models
 */
class TestTakerImporter extends RdsUserImportService
{
    CONST USER_IMPORTER_TYPE = 'test-taker';

    /**
     * Add test taker role to user to import
     *
     * @param $filePath
     * @param array $extraProperties
     * @param array $options
     * @return \common_report_Report
     * @throws \Exception
     * @throws \common_exception_Error
     */
    public function import($filePath, $extraProperties = [], $options = [])
    {
        $extraProperties[UserRdf::PROPERTY_ROLES] = TaoRoles::DELIVERY;
        $extraProperties['roles'] = TaoRoles::DELIVERY;
        return parent::import($filePath, $extraProperties, $options);
    }

    /**
     * Add rds class to test-taker resource
     *
     * If type $properties exists, use it
     * If there is not then use subject root class
     *
     * @param array $properties
     * @return \core_kernel_classes_Class
     */
    protected function getUserClass(array $properties)
    {
        $testtakerRootClass = $this->getClass(TaoOntology::CLASS_URI_SUBJECT);
        if (isset($properties[OntologyRdf::RDF_TYPE])){
            $class = $this->getClass($properties[OntologyRdf::RDF_TYPE]);
            if ($class->isSubClassOf($testtakerRootClass)) {
                return $class;
            }
        }
        return $testtakerRootClass;
    }

    /**
     * Trigger a TestTakerUpdatedEvent at user import
     *
     * @param \core_kernel_classes_Resource $resource
     * @param array $properties
     * @param string $plainPassword
     */
    protected function triggerUserUpdated(\core_kernel_classes_Resource $resource, array $properties, $plainPassword)
    {
        /** @var EventManager $eventManager */
        $eventManager = $this->getServiceLocator()->get(EventManager::SERVICE_ID);
        $eventManager->trigger(new TestTakerUpdatedEvent(
            $resource->getUri(),
            array_merge(
                $properties,
                [
                    'hashForKey' => UserHashForEncryption::hash($plainPassword)
                ]
            )
        ));
    }
}