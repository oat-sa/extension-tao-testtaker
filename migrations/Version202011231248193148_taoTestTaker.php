<?php

declare(strict_types=1);

namespace oat\taoTestTaker\migrations;

use Doctrine\DBAL\Schema\Schema;
use oat\tao\scripts\tools\migrations\AbstractMigration;
use oat\taoTestTaker\models\TestTakerFormService;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version202011231248193148_taoTestTaker extends AbstractMigration
{

    public function getDescription(): string
    {
        return 'Register TestTakerFormService';
    }

    public function up(Schema $schema): void
    {
        $this->registerService(TestTakerFormService::SERVICE_ID, new TestTakerFormService());
    }

    public function down(Schema $schema): void
    {
        $this->getServiceManager()->unregister(TestTakerFormService::SERVICE_ID);
    }
}
