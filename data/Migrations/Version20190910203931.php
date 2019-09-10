<?php

namespace Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20190910203931 extends AbstractMigration
{
    /**
     * @param Schema $schema
     * @throws \Doctrine\DBAL\Migrations\AbortMigrationException
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user 
            CHANGE email email VARCHAR(255) NOT NULL, 
            CHANGE name name VARCHAR(255) NOT NULL, 
            CHANGE surname surname VARCHAR(255) NOT NULL, 
            CHANGE password password CHAR(32) NOT NULL, 
            CHANGE status status INT UNSIGNED NOT NULL, 
            CHANGE role role VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649BF396750 FOREIGN KEY (id) REFERENCES setting (userId)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649BF396750 ON user (id)');
        $this->addSql('ALTER TABLE user RENAME INDEX email TO email_idx');
    }

    /**
     * @param Schema $schema
     * @throws \Doctrine\DBAL\Migrations\AbortMigrationException
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649BF396750');
        $this->addSql('DROP INDEX UNIQ_8D93D649BF396750 ON user');
        $this->addSql('ALTER TABLE user 
            CHANGE email email VARCHAR(255) DEFAULT NULL COLLATE utf8_general_ci, 
            CHANGE name name VARCHAR(50) DEFAULT NULL COLLATE utf8_general_ci, 
            CHANGE surname surname VARCHAR(128) NOT NULL COLLATE utf8_general_ci, 
            CHANGE password password VARCHAR(128) NOT NULL COLLATE utf8_general_ci, 
            CHANGE status status TINYINT(1) DEFAULT \'0\' NOT NULL, 
            CHANGE role role VARCHAR(10) DEFAULT \'0\' NOT NULL COLLATE utf8_general_ci');
        $this->addSql('ALTER TABLE user RENAME INDEX email_idx TO email');
    }
}
