<?php

use oat\taoTestTaker\models\RdfImporter;

return new oat\oatbox\config\ConfigurationService(array(
    RdfImporter::OPTION_STRATEGY => RdfImporter::OPTION_STRATEGY_FAIL_ON_DUPLICATE
));
