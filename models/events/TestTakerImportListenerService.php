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
 * Copyright (c) 2018  (original work) Open Assessment Technologies SA;
 */

namespace oat\taoTestTaker\models\events;

use core_kernel_classes_Resource;
use oat\generis\Helper\UserHashForEncryption;
use oat\generis\model\GenerisRdf;
use oat\generis\model\OntologyAwareTrait;
use oat\oatbox\event\EventManagerAwareTrait;
use oat\oatbox\log\LoggerAwareTrait;
use oat\oatbox\service\ConfigurableService;
use common_report_Report as Report;
use oat\tao\model\event\CsvImportEvent;
use oat\taoTestTaker\models\TestTakerSavePasswordInMemory;
use oat\taoTestTaker\models\TestTakerService;

class TestTakerImportListenerService extends ConfigurableService
{
    const SERVICE_ID = 'taoTestTaker/TestTakerImportListener';

    use EventManagerAwareTrait;
    use OntologyAwareTrait;
    use LoggerAwareTrait;

    /**
     * @param CsvImportEvent $event
     */
    public function catchCsvImportEvent(CsvImportEvent $event)
    {
        $report = $event->getReport();
        /** @var Report $success */
        foreach ($report->getSuccesses() as $success) {
            $resource = $success->getData();
            if ($resource instanceof core_kernel_classes_Resource
                && $this->isTestTakerResource($resource)
            ) {
                try {
                    $this->getEventManager()->trigger(
                        new TestTakerImportedEvent($resource->getUri(), $this->getProperties($resource))
                    );
                } catch (\Exception $e) {
                    $this->logError($e->getMessage());
                }
            }
        }
    }

    /**
     * @param core_kernel_classes_Resource $resource
     * @return bool
     */
    protected function isTestTakerResource(core_kernel_classes_Resource $resource)
    {
       return $resource->isInstanceOf($this->getClass(TestTakerService::CLASS_URI_SUBJECT));
    }


    /**
     * @param core_kernel_classes_Resource $resource
     * @return array
     * @throws \core_kernel_persistence_Exception
     * @throws \common_ext_ExtensionException
     */
    protected function getProperties($resource)
    {
        /** @var \common_ext_ExtensionsManager $extManager */
        $extManager = $this->getServiceLocator()->get(\common_ext_ExtensionsManager::SERVICE_ID);
        $taoTestTaker = $extManager->getExtensionById('taoTestTaker');
        $config = $taoTestTaker->getConfig('csvImporterCallbacks');

        if ((bool)$config['use_properties_for_event']) {
            return [
                'hashForKey'                       => UserHashForEncryption::hash(TestTakerSavePasswordInMemory::getPassword()),
                GenerisRdf::PROPERTY_USER_PASSWORD => $resource->getOnePropertyValue(
                    new \core_kernel_classes_Property(GenerisRdf::PROPERTY_USER_PASSWORD)
                )->literal
            ];
        }

        return [];
    }
}