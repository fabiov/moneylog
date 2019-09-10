<?php

namespace Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20190909175311 extends AbstractMigration
{
    /**
     * @param Schema $schema
     * @throws \Doctrine\DBAL\Migrations\AbortMigrationException
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE movement DROP FOREIGN KEY fkCategoryId');
        $this->addSql('ALTER TABLE movement CHANGE description description VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE movement ADD CONSTRAINT FK_F4DD95F79C370B71 FOREIGN KEY (categoryId) REFERENCES category (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE movement RENAME INDEX fk_account_id TO IDX_F4DD95F762DEB3E8');
        $this->addSql('ALTER TABLE movement RENAME INDEX fkcategoryid TO IDX_F4DD95F79C370B71');
        $this->addSql('UPDATE category SET created = NULL WHERE id < 8');
        $this->addSql('ALTER TABLE category CHANGE status status INT NOT NULL');
        $this->addSql('ALTER TABLE category CHANGE created created DATETIME DEFAULT CURRENT_TIMESTAMP, CHANGE updated updated DATETIME DEFAULT NULL');
    }

    /**
     * @param Schema $schema
     * @throws \Doctrine\DBAL\Migrations\AbortMigrationException
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE category CHANGE status status TINYINT(1) NOT NULL, CHANGE created created DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE updated updated DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE movement DROP FOREIGN KEY FK_F4DD95F79C370B71');
        $this->addSql('ALTER TABLE movement CHANGE description description VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE movement ADD CONSTRAINT fkCategoryId FOREIGN KEY (categoryId) REFERENCES category (id)');
        $this->addSql('ALTER TABLE movement RENAME INDEX idx_f4dd95f762deb3e8 TO fk_account_id');
        $this->addSql('ALTER TABLE movement RENAME INDEX idx_f4dd95f79c370b71 TO fkCategoryId');
    }
}
