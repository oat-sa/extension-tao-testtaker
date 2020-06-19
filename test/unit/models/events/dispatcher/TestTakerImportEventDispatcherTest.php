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
 * Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2020 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 */

namespace oat\taoTestTaker\test\unit\models\events\dispatcher;

use common_report_Report;
use core_kernel_classes_Resource;
use oat\generis\test\TestCase;
use oat\oatbox\event\EventManager;
use oat\taoTestTaker\models\events\dispatcher\TestTakerImportEventDispatcher;
use oat\taoTestTaker\models\events\TestTakerImportedEvent;
use PHPUnit\Framework\MockObject\MockObject;

class TestTakTestTakerImportEventDispatcherTesterTest extends TestCase
{
    /** @var TestTakerImportEventDispatcher */
    protected $subject = null;

    /** @var EventManager|MockObject */
    protected $eventManager = null;

    public function setUp(): void
    {
        $this->eventManager = $this->createMock(EventManager::class);
        $this->subject = new TestTakerImportEventDispatcher();
        $this->subject->setServiceLocator(
            $this->getServiceLocatorMock(
                [
                    EventManager::SERVICE_ID => $this->eventManager
                ]
            )
        );
    }

    public function testDispatch(): void
    {
        $resourceUri = 'abc123';
        $report = $this->createMock(common_report_Report::class);
        $successReport = $this->createMock(common_report_Report::class);
        $resource = $this->createMock(core_kernel_classes_Resource::class);

        $report->method('getSuccesses')
            ->willReturn(
                [
                    $successReport
                ]
            );

        $successReport->method('getData')
            ->willReturn($resource);

        $resource->method('getUri')
            ->willReturn($resourceUri);

        $this->eventManager
            ->expects($this->once())
            ->method('trigger')
            ->with(
                new TestTakerImportedEvent(
                    $resourceUri,
                    [
                        'uri' => $resourceUri
                    ]
                )
            );

        $this->subject->dispatch(
            $report,
            function (core_kernel_classes_Resource $resource) {
                return [
                    'uri' => $resource->getUri()
                ];
            }
        );
    }
}
