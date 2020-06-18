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

declare(strict_types=1);

namespace oat\taoTestTaker\models;

use core_kernel_classes_Resource;
use oat\taoTestTaker\models\events\dispatcher\TestTakerImportEventDispatcher;
use tao_models_classes_import_RdfImporter;

class RdfImporter extends tao_models_classes_import_RdfImporter
{
    public function import($class, $form, $userId = null)
    {
        $report = parent::import($class, $form);

        $this->getTestTakerImportEventDispatcher()
            ->dispatch(
                $report,
                function ($resource)
                {
                    return $this->getProperties($resource);
                }
            );

        return $report;
    }

    protected function getProperties(core_kernel_classes_Resource $resource): array
    {
        return [];
    }

    private function getTestTakerImportEventDispatcher(): TestTakerImportEventDispatcher
    {
        return $this->getServiceLocator()->get(TestTakerImportEventDispatcher::class);
    }
}
