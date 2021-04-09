<?php

namespace Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20210409194359 extends AbstractMigration
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

        $this->addSql('ALTER TABLE account DROP created, CHANGE userId userId INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE account ADD CONSTRAINT FK_7D3656A464B64DCC FOREIGN KEY (userId) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_7D3656A464B64DCC ON account (userId)');
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

        $this->addSql('ALTER TABLE account DROP FOREIGN KEY FK_7D3656A464B64DCC');
        $this->addSql('DROP INDEX IDX_7D3656A464B64DCC ON account');
        $this->addSql('ALTER TABLE account ADD created DATETIME NOT NULL, CHANGE userId userId INT UNSIGNED NOT NULL');
    }
}
