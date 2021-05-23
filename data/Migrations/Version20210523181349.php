<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210523181349 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql('ALTER TABLE setting 
            CHANGE payday paypay SMALLINT UNSIGNED DEFAULT 1 NOT NULL, 
            CHANGE monthsretrospective months SMALLINT UNSIGNED DEFAULT 12 NOT NULL, 
            CHANGE stored provisioning TINYINT(1) DEFAULT 0 NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql('ALTER TABLE setting 
            CHANGE paypay payDay SMALLINT UNSIGNED DEFAULT 1 NOT NULL, 
            CHANGE months monthsRetrospective SMALLINT UNSIGNED DEFAULT 12 NOT NULL, 
            CHANGE provisioning stored TINYINT(1) DEFAULT 0 NOT NULL');
    }
}
