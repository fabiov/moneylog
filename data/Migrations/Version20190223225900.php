<?php

namespace Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20190223225900 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql('RENAME TABLE Account TO account');
        $this->addSql('RENAME TABLE Category TO category');
        $this->addSql('RENAME TABLE Movement TO movement');
        $this->addSql('RENAME TABLE Setting TO setting');
        $this->addSql('RENAME TABLE User TO user');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->addSql('RENAME TABLE account TO Account');
        $this->addSql('RENAME TABLE category TO Category');
        $this->addSql('RENAME TABLE movement TO Movement');
        $this->addSql('RENAME TABLE setting TO Setting');
        $this->addSql('RENAME TABLE user TO User');
    }
}
