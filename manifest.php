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
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 *
 */

/*
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 *
 */

use oat\taoTestTaker\models\routing\ApiRoute;
use oat\taoTestTaker\models\TestTakerService;
use oat\taoTestTaker\scripts\install\SetupConfig;
use oat\taoTestTaker\scripts\install\SetupTesttakerCsvImporter;

$extpath = dirname(__FILE__) . DIRECTORY_SEPARATOR;
$taopath = dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'tao' . DIRECTORY_SEPARATOR;

return [
    'name' => 'taoTestTaker',
    'label' => 'Test-taker core extension',
    'description' => 'TAO TestTaker extension',
    'license' => 'GPL-2.0',
    'version' => '7.7.2.1',
    'author' => 'Open Assessment Technologies, CRP Henri Tudor',
    'requires' => [
        'generis' => '>=12.15.0',
        'tao' => '>=41.1.1',
        'taoBackOffice' => '>=3.0.0'
    ],
    'models' => [
        'http://www.tao.lu/Ontologies/TAOSubject.rdf'
    ],
    'install' => [
        'rdf' => [
                dirname(__FILE__) . '/models/ontology/taosubject.rdf'
        ],
        'php' => [
            SetupConfig::class,
            SetupTesttakerCsvImporter::class,
        ]
    ],
    'update' => "oat\\taoTestTaker\\scripts\\update\\Updater",
    'managementRole' => 'http://www.tao.lu/Ontologies/TAOSubject.rdf#SubjectsManagerRole',
    'acl' => [
        ['grant', TestTakerService::ROLE_SUBJECT_MANAGER, ['ext' => 'taoTestTaker']]
    ],
    'routes' => [
        '/taoTestTaker/api' => ['class' => ApiRoute::class],
        '/taoTestTaker' => 'oat\\taoTestTaker\\actions'
    ],
    'constants' => [
        # actions directory
        "DIR_ACTIONS"           => $extpath . "actions" . DIRECTORY_SEPARATOR,

        # views directory
        "DIR_VIEWS"             => $extpath . "views" . DIRECTORY_SEPARATOR,

        # default module name
        'DEFAULT_MODULE_NAME'   => 'TestTaker',

        #default action name
        'DEFAULT_ACTION_NAME'   => 'index',

        #BASE PATH: the root path in the file system (usually the document root)
        'BASE_PATH'             => $extpath,

        #BASE URL (usually the domain root)
        'BASE_URL'              => ROOT_URL . 'taoTestTaker/',
    ]
];
