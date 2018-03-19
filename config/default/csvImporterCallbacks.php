<?php

use oat\generis\model\GenerisRdf;

return new oat\oatbox\config\ConfigurationService(array(
    'config' => array(
        'callbacks' => array(
            '*' => array('trim'),
            GenerisRdf::PROPERTY_USER_PASSWORD => array('oat\taoTestTaker\models\CsvImporter::taoSubjectsPasswordEncode')
        ),
        'use_properties_for_event' => false
    )
));
