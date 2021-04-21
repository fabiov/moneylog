<?php

namespace Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20210409131304 extends AbstractMigration
{
    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     * @throws \Doctrine\DBAL\Migrations\AbortMigrationException
     */
    public function up(Schema $schema)
    {
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql('ALTER TABLE account 
            ADD closed TINYINT(1) DEFAULT \'0\' NOT NULL, 
            DROP updated, 
            CHANGE recap recap INT NOT NULL, 
            CHANGE created created DATETIME NOT NULL');
    }

    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     * @throws \Doctrine\DBAL\Migrations\AbortMigrationException
     */
    public function down(Schema $schema)
    {
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql('ALTER TABLE account 
            ADD updated DATETIME DEFAULT NULL, 
            DROP closed, 
            CHANGE recap recap TINYINT(1) DEFAULT \'0\' NOT NULL, 
            CHANGE created created DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
    }
}
