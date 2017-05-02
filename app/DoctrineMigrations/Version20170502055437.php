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

        $this->addSql('ALTER TABLE users ENGINE=INNODB');

        $this->addSql('CREATE TABLE roles
        (
            id BIGINT(20) unsigned NOT NULL PRIMARY KEY AUTO_INCREMENT,
            user_id BIGINT(20) unsigned NOT NULL,
            role VARCHAR(30) NOT NULL,
            CONSTRAINT role_users_userid_fk FOREIGN KEY (user_id) REFERENCES users (userid) ON DELETE CASCADE
        );');
        $this->addSql('INSERT INTO roles(user_id,role) SELECT userid,CASE WHEN type = "dj" THEN "ROLE_DJ" WHEN type = "staff" THEN "ROLE_STAFF" ELSE "ROLE_USER" END FROM users');

        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE users DROP type');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->addSql('ALTER TABLE users ADD type enum("","dj","staff") NOT NULL ');
        $this->addSql('DROP TABLE roles');
    }
}
