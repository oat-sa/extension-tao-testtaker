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
 * Copyright (c) 2020 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 */

namespace oat\taoTestTaker\models;

use common_Logger;
use common_report_Report;
use core_kernel_classes_Resource;
use Exception;
use oat\taoTestTaker\models\events\TestTakerImportedEvent;
use tao_models_classes_import_RdfImporter;

class RdfImporter extends tao_models_classes_import_RdfImporter
{
    public function import($class, $form, $userId = null)
    {
        $report = parent::import($class, $form);

        /** @var common_report_Report $success */
        foreach ($report->getSuccesses() as $success) {
            $resource = $success->getData();

            try {
                $this->getEventManager()->trigger(
                    new TestTakerImportedEvent($resource->getUri(), $this->getProperties($resource))
                );
            } catch (Exception $e) {
                common_Logger::e($e->getMessage());
            }
        }

        return $report;
    }

    protected function getProperties(core_kernel_classes_Resource $resource): array
    {
        return [];
    }
}
