<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170502055437 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {

        $this->addSql('ALTER TABLE user ENGINE=INNODB');

        $this->addSql('CREATE TABLE user_role
        (
            id BIGINT(20) unsigned NOT NULL PRIMARY KEY AUTO_INCREMENT,
            user_id BIGINT(20) unsigned NOT NULL,
            role VARCHAR(30) NOT NULL,
            CONSTRAINT role_users_id_fk FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
        );');
        $this->addSql('INSERT INTO user_role(user_id,role) SELECT id,"ROLE_USER"');

        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP type');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->addSql('ALTER TABLE user ADD type enum("","dj","staff") NOT NULL ');
        $this->addSql('DROP TABLE user_role');
    }
}
