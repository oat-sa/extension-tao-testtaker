<?php

use oat\generis\model\GenerisRdf;

return new oat\oatbox\config\ConfigurationService([
    'config' => [
        'callbacks' => [
            '*' => ['trim'],
            GenerisRdf::PROPERTY_USER_PASSWORD => ['oat\taoTestTaker\models\CsvImporter::taoSubjectsPasswordEncode'],
            GenerisRdf::PROPERTY_USER_DEFLG => ['\tao_models_classes_LanguageService::filterLanguage'],
        ],
        'use_properties_for_event' => false
    ]
]);
