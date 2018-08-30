<?php

use oat\generis\model\GenerisRdf;

return new oat\oatbox\config\ConfigurationService(array(
    'config' => array(
        'callbacks' => array(
            '*' => array('trim'),
            GenerisRdf::PROPERTY_USER_PASSWORD => array('oat\taoTestTaker\models\CsvImporter::taoSubjectsPasswordEncode'),
            GenerisRdf::PROPERTY_USER_DEFLG => array('\tao_models_classes_LanguageService::filterLanguage'),
        ),
        'use_properties_for_event' => false
    )
));
