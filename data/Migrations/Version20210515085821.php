<?php declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210515085821 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql('ALTER TABLE provision CHANGE userId userId INT UNSIGNED DEFAULT NULL, CHANGE importo importo NUMERIC(8, 2) NOT NULL, CHANGE descrizione descrizione VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE provision ADD CONSTRAINT FK_BA9B429064B64DCC FOREIGN KEY (userId) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_BA9B429064B64DCC ON provision (userId)');
        $this->addSql('DROP INDEX UNIQ_8D93D649BF396750 ON user');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql('ALTER TABLE provision DROP FOREIGN KEY FK_BA9B429064B64DCC');
        $this->addSql('DROP INDEX IDX_BA9B429064B64DCC ON provision');
        $this->addSql('ALTER TABLE provision CHANGE importo importo DOUBLE PRECISION DEFAULT NULL, CHANGE descrizione descrizione VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`, CHANGE userId userId INT UNSIGNED NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649BF396750 ON user (id)');
    }
}
