<?php declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210515191639 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql('ALTER TABLE setting 
            DROP created, 
            DROP updated, 
            CHANGE payDay payDay SMALLINT UNSIGNED DEFAULT 1 NOT NULL,
            CHANGE monthsRetrospective monthsRetrospective SMALLINT UNSIGNED DEFAULT 12 NOT NULL');
        $this->addSql('ALTER TABLE setting 
            ADD CONSTRAINT FK_9F74B89864B64DCC FOREIGN KEY (userId) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user DROP created, DROP updated');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql('ALTER TABLE setting DROP FOREIGN KEY FK_9F74B89864B64DCC');
        $this->addSql('ALTER TABLE setting 
            ADD created DATETIME DEFAULT CURRENT_TIMESTAMP, 
            ADD updated DATETIME DEFAULT NULL, 
            CHANGE payDay payDay TINYINT(1) DEFAULT \'0\' NOT NULL, 
            CHANGE monthsRetrospective monthsRetrospective INT UNSIGNED DEFAULT 12 NOT NULL');
        $this->addSql('ALTER TABLE user 
            ADD created DATETIME DEFAULT CURRENT_TIMESTAMP, 
            ADD updated DATETIME DEFAULT NULL');
    }
}
