<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170525182855 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE blog ADD name VARCHAR(100) NOT NULL, CHANGE is_pinned is_pinned TINYINT(1) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C01551435E237E06 ON blog (name)');
        $this->addSql('ALTER TABLE user CHANGE phone phone VARCHAR(30) NOT NULL, CHANGE last_login last_login DATETIME NOT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX UNIQ_C01551435E237E06 ON blog');
        $this->addSql('ALTER TABLE blog DROP name, CHANGE is_pinned is_pinned TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE phone phone VARCHAR(30) DEFAULT NULL COLLATE utf8_unicode_ci, CHANGE last_login last_login DATETIME DEFAULT NULL');
    }
}
