<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210926172529 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql('ALTER TABLE account ADD status ENUM(\'closed\', \'open\', \'highlight\') NOT NULL DEFAULT \'open\'');

        $this->addSql('UPDATE account SET status=\'HIGHLIGHT\' WHERE recap=1');
        $this->addSql('UPDATE account SET status=\'CLOSED\' WHERE closed=1');

        $this->addSql('ALTER TABLE account DROP recap, DROP closed');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql('ALTER TABLE account ADD recap INT NOT NULL, ADD closed TINYINT(1) DEFAULT \'0\' NOT NULL');

        $this->addSql('UPDATE account SET recap=1 WHERE status=\'highlight\'');
        $this->addSql('UPDATE account SET closed=1 WHERE status=\'closed\'');

        $this->addSql('ALTER TABLE account DROP status');
    }
}
