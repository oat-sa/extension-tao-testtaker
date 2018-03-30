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
 * Copyright (c) 2018 Open Assessment Technologies SA
 */

namespace oat\taoTestTaker\scripts\install;

use oat\oatbox\extension\InstallAction;
use oat\tao\model\user\import\UserCsvImporterFactory;
use oat\taoTestTaker\models\TestTakerImporter;

class SetupTesttakerCsvImporter extends InstallAction
{
    /**
     * @param $params
     * @return \common_report_Report
     * @throws \common_Exception
     */
    public function __invoke($params)
    {
        $importerFactory = $this->getServiceLocator()->get(UserCsvImporterFactory::SERVICE_ID);
        $typeOptions = $importerFactory->getOption(UserCsvImporterFactory::OPTION_MAPPERS);
        $typeOptions[TestTakerImporter::USER_IMPORTER_TYPE] = array(
            UserCsvImporterFactory::OPTION_MAPPERS_IMPORTER => new TestTakerImporter()
        );
        $importerFactory->setOption(UserCsvImporterFactory::OPTION_MAPPERS, $typeOptions);
        $this->registerService(UserCsvImporterFactory::SERVICE_ID, $importerFactory);

        return \common_report_Report::createSuccess('Testtaker csv importer successfully registered.');
    }

}