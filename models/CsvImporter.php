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
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 *
 */
namespace oat\taoTestTaker\models;
use common_Logger;
use common_report_Report;
use core_kernel_classes_Resource;
use oat\generis\Helper\UserHashForEncryption;
use oat\oatbox\service\ServiceManager;
use oat\oatbox\user\UserLanguageService;
use oat\tao\model\TaoOntology;
use oat\generis\model\GenerisRdf;
use oat\taoTestTaker\models\events\TestTakerImportedEvent;

/**
 * A custom subject CSV importer
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package taoSubjects

 */
class CsvImporter extends \tao_models_classes_import_CsvImporter
{
    public function import($class, $form)
    {
        $report = parent::import($class, $form);

        /** @var common_report_Report $success */
        foreach ($report->getSuccesses() as $success) {
            $resource = $success->getData();
            try {
                $this->getEventManager()->trigger(
                    new TestTakerImportedEvent($resource->getUri(), $this->getProperties($resource))
                );
            } catch (\Exception $e) {
                common_Logger::e($e->getMessage());
            }
        }

        return $report;
    }

    public function getValidators()
    {
        return [
            GenerisRdf::PROPERTY_USER_LOGIN => [\tao_helpers_form_FormFactory::getValidator('Unique')],
            GenerisRdf::PROPERTY_USER_UILG => [\tao_helpers_form_FormFactory::getValidator('NotEmpty')],
        ];
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
        $extManager =  ServiceManager::getServiceManager()->get(\common_ext_ExtensionsManager::SERVICE_ID);
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

    /**
     * (non-PHPdoc)
     * @see tao_models_classes_import_CsvImporter::getExludedProperties()
     */
    protected function getExludedProperties()
    {
        return array_merge(parent::getExludedProperties(), array(
            GenerisRdf::PROPERTY_USER_DEFLG,
            GenerisRdf::PROPERTY_USER_ROLES,
            TaoOntology::PROPERTY_USER_LAST_EXTENSION,
            TaoOntology::PROPERTY_USER_FIRST_TIME,
            GenerisRdf::PROPERTY_USER_TIMEZONE
        ));
    }

    /**
     * (non-PHPdoc)
     * @see tao_models_classes_import_CsvImporter::getStaticData()
     */
    protected function getStaticData()
    {
        $lang = \tao_helpers_I18n::getLangResourceByCode(DEFAULT_LANG)->getUri();

        return array(
            GenerisRdf::PROPERTY_USER_DEFLG => $lang,
            GenerisRdf::PROPERTY_USER_TIMEZONE => TIME_ZONE,
            GenerisRdf::PROPERTY_USER_ROLES => TaoOntology::PROPERTY_INSTANCE_ROLE_DELIVERY,
        );
    }

    /**
     * (non-PHPdoc)
     * @see tao_models_classes_import_CsvImporter::getAdditionAdapterOptions()
     * @throws \common_ext_ExtensionException
     */
    protected function getAdditionAdapterOptions()
    {
        /** @var \common_ext_ExtensionsManager $extManager */
        $extManager = ServiceManager::getServiceManager()->get(\common_ext_ExtensionsManager::SERVICE_ID);
        $taoTestTaker = $extManager->getExtensionById('taoTestTaker');
        $config = $taoTestTaker->getConfig('csvImporterCallbacks');

        if (empty($config['callbacks'])){
            $returnValue = array(
                'callbacks' => array(
                    '*' => array('trim'),
                    GenerisRdf::PROPERTY_USER_PASSWORD => array('oat\taoTestTaker\models\CsvImporter::taoSubjectsPasswordEncode')
                )
            );
        } else {
            $returnValue = array(
                'callbacks' => $config['callbacks']
            );
        }

        return $returnValue;
    }

    /**
     * Wrapper for password hash
     *
     * @param  string $value
     * @return string
     */
    public static function taoSubjectsPasswordEncode($value)
    {
        return \core_kernel_users_Service::getPasswordHash()->encrypt($value);
    }

}
