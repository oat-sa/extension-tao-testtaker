<?php

declare(strict_types=1);

namespace oat\taoTestTaker\migrations;

use Doctrine\DBAL\Schema\Schema;
use oat\oatbox\config\ConfigurationService;
use oat\tao\scripts\tools\migrations\AbstractMigration;
use oat\taoTestTaker\models\RdfImporter;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version202105041023253148_taoTestTaker extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Register validation config for `' . RdfImporter::class . '`';
    }

    public function up(Schema $schema): void
    {
        $this->getServiceManager()->register(
            RdfImporter::CONFIG_ID,
            new ConfigurationService(
                [
                    RdfImporter::OPTION_STRATEGY => RdfImporter::OPTION_STRATEGY_FAIL_ON_DUPLICATE
                ]
            )
        );
    }

    public function down(Schema $schema): void
    {
        //Reverting to default behaviour for RdfImporter - import even duplicated TTs
        $this->getServiceManager()->register(
            RdfImporter::CONFIG_ID,
            new ConfigurationService(
                [
                    RdfImporter::OPTION_STRATEGY => RdfImporter::OPTION_STRATEGY_IMPORT_ON_DUPLICATE
                ]
            )
        );
    }
}
