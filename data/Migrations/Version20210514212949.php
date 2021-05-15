<?php declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210514212949 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql('DELETE FROM category WHERE userId NOT IN (SELECT id FROM user);');
        $this->addSql('ALTER TABLE category DROP created, DROP updated, CHANGE userId userId INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE category ADD CONSTRAINT FK_64C19C164B64DCC FOREIGN KEY (userId) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_64C19C164B64DCC ON category (userId)');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql('ALTER TABLE category DROP FOREIGN KEY FK_64C19C164B64DCC');
        $this->addSql('DROP INDEX IDX_64C19C164B64DCC ON category');
        $this->addSql('ALTER TABLE category ADD created DATETIME DEFAULT CURRENT_TIMESTAMP, ADD updated DATETIME DEFAULT NULL, CHANGE userId userId INT UNSIGNED NOT NULL');
    }
}
