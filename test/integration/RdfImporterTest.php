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
 * Copyright (c) 2021 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 */

declare(strict_types=1);

namespace oat\taoTestTaker\test\functional;

use core_kernel_classes_Class;
use core_kernel_classes_Resource;
use oat\oatbox\config\ConfigurationService;
use oat\oatbox\event\EventManager;
use oat\oatbox\filesystem\Directory;
use oat\oatbox\filesystem\File;
use oat\oatbox\log\LoggerService;
use oat\tao\model\TaoOntology;
use oat\tao\model\upload\UploadService;
use oat\taoTestTaker\models\events\dispatcher\TestTakerImportEventDispatcher;
use oat\taoTestTaker\models\RdfImporter;
use oat\generis\test\TestCase;
use oat\taoTestTaker\models\TestTakerService;
use common_report_Report as Report;

class RdfImporterTest extends TestCase
{
    private const SINGLE_TT_RDF = 'single_testtaker.rdf';
    private const MULTI_TT_RDF = 'multi_testtaker.rdf';

    private const ADMIN_USER = LOCAL_NAMESPACE . TaoOntology::DEFAULT_USER_URI_SUFFIX;

    /** @var RdfImporter $subject */
    private $subject;

    private $importedUris = [];

    private $currentFile;

    public function setUp(): void
    {
        parent::setUp();

        $this->subject = new RdfImporter();
    }

    public function testItFailsWithUnknownStrategyConfigured(): void
    {
        $this->currentFile = self::SINGLE_TT_RDF;

        $form = [
            'uploaded_file' => $this->currentFile
        ];

        $this->switchStrategy('unknown');

        $importReport = $this->subject->import(
            new core_kernel_classes_Class(TestTakerService::CLASS_URI_SUBJECT),
            $form,
            self::ADMIN_USER
        );

        self::assertEquals(true, $importReport->containsError());
        self::assertCount(1, $importReport->getErrors(true));
        self::assertEquals('Data import failed', $importReport->getMessage());
        self::assertEquals(
            'oat\taoTestTaker\models\RdfImporter configured incorrectly',
            $importReport->getErrors(true)[0]->getMessage()
        );

        $this->grabImportedUris($importReport);

        self::assertCount(0, $this->importedUris);
    }

    public function testItFailsWithFailOnDuplicateStrategyWhenOneUserInFile(): void
    {
        $this->currentFile = self::SINGLE_TT_RDF;

        $form = [
            'uploaded_file' => $this->currentFile
        ];

        $this->switchStrategy(RdfImporter::OPTION_STRATEGY_FAIL_ON_DUPLICATE);

        $importReport = $this->subject->import(
            new core_kernel_classes_Class(TestTakerService::CLASS_URI_SUBJECT),
            $form,
            self::ADMIN_USER
        );

        self::assertEquals(false, $importReport->containsError());

        $this->grabImportedUris($importReport);

        self::assertCount(1, $this->importedUris);

        $importReport = $this->subject->import(
            new core_kernel_classes_Class(TestTakerService::CLASS_URI_SUBJECT),
            $form,
            self::ADMIN_USER
        );

        self::assertEquals(true, $importReport->containsError());

        $this->grabImportedUris($importReport);

        self::assertCount(1, $this->importedUris);
    }

    public function testItFailsWithFailOnDuplicateStrategyWhenMultipleUserInFile(): void
    {
        $this->currentFile = self::MULTI_TT_RDF;

        $form = [
            'uploaded_file' => $this->currentFile
        ];

        $this->switchStrategy(RdfImporter::OPTION_STRATEGY_FAIL_ON_DUPLICATE);

        $importReport = $this->subject->import(
            new core_kernel_classes_Class(TestTakerService::CLASS_URI_SUBJECT),
            $form,
            self::ADMIN_USER
        );

        self::assertEquals(true, $importReport->containsError());

        $this->grabImportedUris($importReport);

        self::assertCount(2, $this->importedUris);
    }

    public function testItSkipsWithSkipOnDuplicateStrategyWhenSingleUserInFile(): void
    {
        $this->currentFile = self::SINGLE_TT_RDF;

        $form = [
            'uploaded_file' => $this->currentFile
        ];

        $this->switchStrategy(RdfImporter::OPTION_STRATEGY_SKIP_ON_DUPLICATE);

        $importReport = $this->subject->import(
            new core_kernel_classes_Class(TestTakerService::CLASS_URI_SUBJECT),
            $form,
            self::ADMIN_USER
        );

        self::assertEquals(false, $importReport->containsError());

        $this->grabImportedUris($importReport);

        self::assertCount(1, $this->importedUris);

        $importReport = $this->subject->import(
            new core_kernel_classes_Class(TestTakerService::CLASS_URI_SUBJECT),
            $form,
            self::ADMIN_USER
        );

        self::assertEquals(false, $importReport->containsError());
        self::assertEquals(true, $importReport->contains(Report::TYPE_WARNING));
        self::assertCount(2, $this->getWarningReports($importReport));

        $this->grabImportedUris($importReport);

        self::assertCount(1, $this->importedUris);
    }

    public function testItSkipsWithSkipOnDuplicateStrategyWhenMultipleUserInFile(): void
    {
        $this->currentFile = self::MULTI_TT_RDF;

        $form = [
            'uploaded_file' => $this->currentFile
        ];

        $this->switchStrategy(RdfImporter::OPTION_STRATEGY_SKIP_ON_DUPLICATE);

        $importReport = $this->subject->import(
            new core_kernel_classes_Class(TestTakerService::CLASS_URI_SUBJECT),
            $form,
            self::ADMIN_USER
        );

        self::assertEquals(false, $importReport->containsError());
        self::assertEquals(true, $importReport->contains(Report::TYPE_WARNING));
        self::assertCount(2, $this->getWarningReports($importReport));

        $this->grabImportedUris($importReport);

        self::assertCount(3, $this->importedUris);

        $importReport = $this->subject->import(
            new core_kernel_classes_Class(TestTakerService::CLASS_URI_SUBJECT),
            $form,
            self::ADMIN_USER
        );

        self::assertEquals(false, $importReport->containsError());
        self::assertEquals(true, $importReport->contains(Report::TYPE_WARNING));
        self::assertCount(8, $this->getWarningReports($importReport));

        $this->grabImportedUris($importReport);

        self::assertCount(3, $this->importedUris);
    }

    public function testItImportsWithImportOnDuplicateStrategyWhenOneUserInFile(): void
    {
        $this->currentFile = self::SINGLE_TT_RDF;

        $form = [
            'uploaded_file' => $this->currentFile
        ];

        $this->switchStrategy(RdfImporter::OPTION_STRATEGY_IMPORT_ON_DUPLICATE);

        $importReport = $this->subject->import(
            new core_kernel_classes_Class(TestTakerService::CLASS_URI_SUBJECT),
            $form,
            self::ADMIN_USER
        );

        self::assertEquals(false, $importReport->containsError());

        $this->grabImportedUris($importReport);

        self::assertCount(1, $this->importedUris);

        $importReport = $this->subject->import(
            new core_kernel_classes_Class(TestTakerService::CLASS_URI_SUBJECT),
            $form,
            self::ADMIN_USER
        );

        self::assertEquals(false, $importReport->containsError());
        self::assertEquals(true, $importReport->contains(Report::TYPE_WARNING));
        self::assertCount(1, $this->getWarningReports($importReport));

        $this->grabImportedUris($importReport);

        self::assertCount(2, $this->importedUris);
    }

    public function testItImportsWithImportOnDuplicateStrategyWhenMultipleUserInFile(): void
    {
        $this->currentFile = self::MULTI_TT_RDF;

        $form = [
            'uploaded_file' => $this->currentFile
        ];

        $this->switchStrategy(RdfImporter::OPTION_STRATEGY_IMPORT_ON_DUPLICATE);

        $importReport = $this->subject->import(
            new core_kernel_classes_Class(TestTakerService::CLASS_URI_SUBJECT),
            $form,
            self::ADMIN_USER
        );

        self::assertEquals(false, $importReport->containsError());

        $this->grabImportedUris($importReport);

        self::assertCount(4, $this->importedUris);

        $importReport = $this->subject->import(
            new core_kernel_classes_Class(TestTakerService::CLASS_URI_SUBJECT),
            $form,
            self::ADMIN_USER
        );

        self::assertEquals(false, $importReport->containsError());
        self::assertEquals(true, $importReport->contains(Report::TYPE_WARNING));
        self::assertCount(4, $this->getWarningReports($importReport));

        $this->grabImportedUris($importReport);

        self::assertCount(8, $this->importedUris);
    }

    private function switchStrategy(string $strategy = ''): void
    {
        $fileMock = $this->createMock(File::class);
        $fileMock
            ->method('getBasename')
            ->willReturn(basename($this->currentFile));
        $fileMock
            ->method('exists')
            ->willReturn(true);
        $fileMock
            ->method('read')
            ->willReturn(file_get_contents(__DIR__ . '/../samples/' . $this->currentFile))
        ;

        $directoryMock = $this->createMock(Directory::class);
        $directoryMock
            ->method('getFile')
            ->with($this->currentFile)
            ->willReturn($fileMock);

        $uploadServiceMock = $this->createMock(UploadService::class);
        $uploadServiceMock
            ->method('getUploadDir')
            ->willReturn($directoryMock);

        $eventManagerMock = $this->createMock(EventManager::class);

        $loggerMock = $this->createMock(LoggerService::class);

        $serviceLocator = $this->getServiceLocatorMock(
            [
                RdfImporter::CONFIG_ID => new ConfigurationService(
                    [
                        RdfImporter::OPTION_STRATEGY => $strategy
                    ]
                ),
                TestTakerImportEventDispatcher::class => new TestTakerImportEventDispatcher(),
                UploadService::SERVICE_ID => $uploadServiceMock,
                EventManager::SERVICE_ID => $eventManagerMock,
                LoggerService::SERVICE_ID => $loggerMock
            ]
        );

        $this->subject->setServiceLocator($serviceLocator);
    }

    private function grabImportedUris(Report $report): void
    {
        if (($reportData = $report->getData()) !== null) {
            $this->importedUris[] = $reportData->getUri();
        } else {
            /** @var Report $childReport */
            foreach ($report->getChildren() as $childReport) {
                if (($childData = $childReport->getData()) !== null) {
                    $this->importedUris[] = $childData->getUri();
                } else {
                    $this->grabImportedUris($childReport);
                }
            }
        }
    }

    private function getWarningReports(Report $report): array
    {
        $iterator = new \RecursiveIteratorIterator(
            new \common_report_RecursiveReportIterator($report->getChildren()),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        $warnings = [];
        foreach ($iterator as $element) {
            if ($element->getType() == Report::TYPE_WARNING) {
                $warnings[] = $element;
            }
        }
        return $warnings;
    }

    public function tearDown(): void
    {
        parent::tearDown();

        $this->cleanImportedUsers();
    }

    private function cleanImportedUsers(): void
    {
        foreach ($this->importedUris as $resourceUri) {
            $user = new core_kernel_classes_Resource($resourceUri);
            $user->delete();
        }
    }
}
