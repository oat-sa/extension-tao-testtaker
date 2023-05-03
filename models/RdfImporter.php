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

use common_report_Report as Report;
use common_Utils;
use core_kernel_classes_Class;
use core_kernel_classes_Resource;
use EasyRdf\Format;
use EasyRdf\Graph;
use oat\generis\model\OntologyRdf;
use oat\generis\model\user\UserRdf;
use oat\oatbox\log\LoggerService;
use oat\tao\model\metadata\exception\InconsistencyConfigException;
use oat\tao\model\TaoOntology;
use oat\taoTestTaker\models\events\dispatcher\TestTakerImportEventDispatcher;
use tao_models_classes_import_RdfImporter;

class RdfImporter extends tao_models_classes_import_RdfImporter
{
    public const CONFIG_ID = 'taoTestTaker/rdfImporterConfig';

    public const OPTION_STRATEGY = 'strategy';

    public const OPTION_STRATEGY_FAIL_ON_DUPLICATE = 'fail';
    public const OPTION_STRATEGY_SKIP_ON_DUPLICATE = 'skip';
    public const OPTION_STRATEGY_IMPORT_ON_DUPLICATE = 'import';

    public const AVAILABLE_STRATEGIES = [
        self::OPTION_STRATEGY_FAIL_ON_DUPLICATE,
        self::OPTION_STRATEGY_SKIP_ON_DUPLICATE,
        self::OPTION_STRATEGY_IMPORT_ON_DUPLICATE
    ];

    private const NOT_IMPORTABLE_STRATEGIES = [
        self::OPTION_STRATEGY_FAIL_ON_DUPLICATE,
        self::OPTION_STRATEGY_SKIP_ON_DUPLICATE
    ];

    /** @var $strategy string */
    private $strategy;

    private function readStrategyFromConfig(): string
    {
        $config = $this->getServiceLocator()->get(self::CONFIG_ID);

        $strategy = $config->getOption(self::OPTION_STRATEGY);

        if (!in_array($strategy, self::AVAILABLE_STRATEGIES, true)) {
            $message = sprintf(
                "Bad strategy `%s` configured. Strategy should be one of `[%s]",
                $strategy,
                implode(',', self::AVAILABLE_STRATEGIES)
            );

            $this->getLogger()->logError($message);

            throw new InconsistencyConfigException($message);
        }

        $this->strategy = $strategy;

        return $this->strategy;
    }

    public function import($class, $form, $userId = null)
    {
        $report = parent::import($class, $form);

        $this->getTestTakerImportEventDispatcher()
            ->dispatch(
                $report,
                function ($resource) {
                    return $this->getProperties($resource);
                }
            );

        return $report;
    }

    protected function flatImport($content, core_kernel_classes_Class $class)
    {
        $report = new Report(Report::TYPE_INFO, __('Data import started'));

        $graph = new Graph();
        $graph->parse($content);

        // keep type property
        $map = [
            OntologyRdf::RDF_PROPERTY => OntologyRdf::RDF_PROPERTY
        ];

        foreach ($graph->resources() as $resource) {
            $map[$resource->getUri()] = common_Utils::getNewUri();
        }

        $format = Format::getFormat('php');
        $data = $graph->serialise($format);

        $importedCount = 0;

        try {
            $strategy = $this->readStrategyFromConfig();
        } catch (InconsistencyConfigException $exception) {
            $report->add(
                new Report(
                    Report::TYPE_ERROR,
                    self::class . " configured incorrectly"
                )
            );

            $report->setType(Report::TYPE_ERROR);
            $report->setMessage('Data import failed');

            $this->getLogger()->logError($exception->getMessage());

            return $report;
        }

        foreach ($data as $subjectUri => $propertiesValues) {
            $loginProperty = current($propertiesValues[UserRdf::PROPERTY_LOGIN]);
            $login = $loginProperty['value'];

            $isDuplicated = $this->isDuplicated($login);

            $duplicationReport = null;

            if ($isDuplicated && in_array($strategy, self::NOT_IMPORTABLE_STRATEGIES, true)) {
                $message = "User `%s` was duplicated.";
                $report->add(new Report(Report::TYPE_WARNING, sprintf($message, $login)));

                if ($strategy === self::OPTION_STRATEGY_FAIL_ON_DUPLICATE) {
                    $report->add(
                        new Report(
                            Report::TYPE_ERROR,
                            'Since the `Fail on duplicate` strategy was chosen, import will now fail'
                        )
                    );

                    break;
                }

                if ($strategy === self::OPTION_STRATEGY_SKIP_ON_DUPLICATE) {
                    $report->add(
                        new Report(
                            Report::TYPE_WARNING,
                            'Since the `Skip on duplicate` strategy was chosen, import will now skip this user, without importing it'
                        )
                    );

                    continue;
                }
            }

            $resource = new core_kernel_classes_Resource($map[$subjectUri]);
            $subreport = $this->importProperties($resource, $propertiesValues, $map, $class);
            $report->add($subreport);

            if (!$subreport->containsError()) {
                $importedCount++;
            }

            if ($isDuplicated && $strategy === self::OPTION_STRATEGY_IMPORT_ON_DUPLICATE) {
                $report->add(
                    new Report(
                        Report::TYPE_WARNING,
                        'Since the `Import on duplicate` strategy was chosen, import will import the user, but behaviour is unpredicted'
                    )
                );
            }
        }

        if ($importedCount > 0 && !$report->containsError()) {
            $report->setType(Report::TYPE_SUCCESS);
            $report->setMessage(__('Data imported successfully'));
        }

        return $report;
    }

    private function isDuplicated(string $login): bool
    {
        $baseResourceClassInstance = new core_kernel_classes_Class(TaoOntology::CLASS_URI_SUBJECT);

        $resources = $baseResourceClassInstance
            ->searchInstances([UserRdf::PROPERTY_LOGIN => $login], ['recursive' => true, 'like' => false]);

        return count($resources) > 0;
    }

    protected function getProperties(core_kernel_classes_Resource $resource): array
    {
        return [];
    }

    private function getTestTakerImportEventDispatcher(): TestTakerImportEventDispatcher
    {
        return $this->getServiceLocator()->get(TestTakerImportEventDispatcher::class);
    }

    private function getLogger(): LoggerService
    {
        return $this->getServiceLocator()->get(LoggerService::SERVICE_ID);
    }
}
