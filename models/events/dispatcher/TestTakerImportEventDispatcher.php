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
 * Copyright (c) 2016  (original work) Open Assessment Technologies SA;
 *
 * @author Ivan klimchuk <klimchuk@1pt.com>
 */

namespace oat\taoTestTaker\models\events\dispatcher;

use common_Logger;
use common_report_Report;
use oat\oatbox\event\EventManagerAwareTrait;
use oat\oatbox\service\ConfigurableService;
use oat\taoTestTaker\models\events\TestTakerImportedEvent;
use Throwable;

class TestTakerImportEventDispatcher extends ConfigurableService
{
    use EventManagerAwareTrait;

    public function dispatch(common_report_Report $report, callable $processResource): void
    {
        /** @var common_report_Report $success */
        foreach ($report->getSuccesses() as $success) {
            $resource = $success->getData();

            try {
                $this->getEventManager()->trigger(
                    new TestTakerImportedEvent($resource->getUri(), $processResource($resource))
                );
            } catch (Throwable $e) {
                common_Logger::e($e->getMessage());
            }
        }
    }
}
