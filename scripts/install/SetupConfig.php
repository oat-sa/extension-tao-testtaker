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
 * Copyright (c) 2018(original work) Open Assessment Technologies SA;
 *
 *
 */

namespace oat\taoTestTaker\scripts\install;

use oat\generis\model\GenerisRdf;
use oat\oatbox\extension\InstallAction;

class SetupConfig extends InstallAction
{
    /**
     * @param $params
     * @throws \common_exception_Error
     * @throws \common_ext_ExtensionException
     */
    public function __invoke($params)
    {
        /** @var \common_ext_ExtensionsManager $extManager */
        $extManager = $this->getServiceLocator()->get(\common_ext_ExtensionsManager::SERVICE_ID);
        $taoTestTaker = $extManager->getExtensionById('taoTestTaker');

        $taoTestTaker->setConfig('csvImporterCallbacks', [
            'callbacks' => array(
                '*' => array('trim'),
                GenerisRdf::PROPERTY_USER_PASSWORD => array('oat\taoTestTaker\models\CsvImporter::taoSubjectsPasswordEncode')
            ),
            'use_properties_for_event' => false
        ]);
    }
}