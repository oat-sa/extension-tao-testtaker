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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA;
 *
 *
 */

namespace oat\taoTestTaker\scripts\update;

use oat\generis\model\GenerisRdf;
use oat\tao\model\accessControl\func\AclProxy;
use oat\tao\model\accessControl\func\AccessRule;
use oat\tao\model\user\TaoRoles;
use oat\taoTestTaker\actions\Api;
use oat\tao\model\user\import\UserCsvImporterFactory;
use oat\taoTestTaker\actions\RestTestTakers;
use oat\taoTestTaker\models\TestTakerImporter;
use oat\taoTestTaker\models\TestTakerService;

/**
 * Class Updater
 * @package oat\taoTestTaker\scripts\update
 */
class Updater extends \common_ext_ExtensionUpdater
{
    /**
     * @param $initialVersion
     * @return string $versionUpdatedTo
     * @internal param string $currentVersion
     * @throws \common_ext_ExtensionException
     * @throws \common_Exception
     */
    public function update($initialVersion)
    {
        $this->skip('2.6', '3.0.0');
        // fix anonymous access
        if ($this->isVersion('3.0.0')) {
            AclProxy::revokeRule(new AccessRule(AccessRule::GRANT, TaoRoles::ANONYMOUS, Api::class));
            $this->setVersion('3.0.1');
        }
        $this->skip('3.0.1', '3.4.0');

        if ($this->isVersion('3.4.0')) {

            /** @var \common_ext_ExtensionsManager $extManager */
            $extManager = $this->getServiceManager()->get(\common_ext_ExtensionsManager::SERVICE_ID);
            $taoTestTaker = $extManager->getExtensionById('taoTestTaker');

            $taoTestTaker->setConfig('csvImporterCallbacks', [
                'callbacks' => array(
                    '*' => array('trim'),
                    GenerisRdf::PROPERTY_USER_PASSWORD => array('oat\taoTestTaker\models\CsvImporter::taoSubjectsPasswordEncode')
                ),
                'use_properties_for_event' => false
            ]);

            $this->setVersion('3.5.0');
        }

        $this->skip('3.5.0', '3.6.0');

        if ($this->isVersion('3.6.0')) {
            /** @var UserCsvImporterFactory $importerFactory */
            $importerFactory = $this->getServiceManager()->get(UserCsvImporterFactory::SERVICE_ID);
            $typeOptions = $importerFactory->getOption(UserCsvImporterFactory::OPTION_MAPPERS);
            $typeOptions[TestTakerImporter::USER_IMPORTER_TYPE] = array(
                UserCsvImporterFactory::OPTION_MAPPERS_IMPORTER => new TestTakerImporter()
            );
            $importerFactory->setOption(UserCsvImporterFactory::OPTION_MAPPERS, $typeOptions);
            $this->getServiceManager()->register(UserCsvImporterFactory::SERVICE_ID, $importerFactory);

            $this->setVersion('3.7.0');
        }

        $this->skip('3.7.0', '3.10.2');

        if ($this->isVersion('3.10.2')) {
            /** @var \common_ext_ExtensionsManager $extensionManager */
            $extensionManager = $this->getServiceManager()->get(\common_ext_ExtensionsManager::SERVICE_ID);
            $extension = $extensionManager->getExtensionById('taoTestTaker');
            $config = $extension->getConfig('csvImporterCallbacks');
            $config['callbacks'][GenerisRdf::PROPERTY_USER_UILG] = ['\tao_models_classes_LanguageService::filterLanguage'];
            $extension->setConfig('csvImporterCallbacks', $config);
            $this->setVersion('3.11.0');
        }

        $this->skip('3.11.0', '5.1.1');
    }
}
