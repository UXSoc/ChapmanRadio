<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170504155401 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql('ALTER TABLE shows ENGINE=INNODB');

        $this->addSql('ALTER TABLE shows RENAME TO show;');
        $this->addSql('ALTER TABLE show CHANGE showid id BIGINT(20) unsigned NOT NULL AUTO_INCREMENT;');

        $this->addSql('CREATE TABLE show_user
        (
            id BIGINT(20) unsigned NOT NULL PRIMARY KEY AUTO_INCREMENT,
            show_id BIGINT unsigned NOT NULL,
            user_id BIGINT unsigned NOT NULL,
            CONSTRAINT show_user_users_id_fk FOREIGN KEY (user_id) REFERENCES user (id),
            CONSTRAINT show_user_shows_id_fk FOREIGN KEY (show_id) REFERENCES show (id)
        );');

        //move user ids to the show_user table
        $this->addSql('INSERT INTO show_user(show_id,user_id) SELECT id,userid1 FROM show WHERE userid1 <> 0');
        $this->addSql('INSERT INTO show_user(show_id,user_id) SELECT id,userid2 FROM show WHERE userid2 <> 0');
        $this->addSql('INSERT INTO show_user(show_id,user_id) SELECT id,userid3 FROM show WHERE userid3 <> 0');
        $this->addSql('INSERT INTO show_user(show_id,user_id) SELECT id,userid4 FROM show WHERE userid4 <> 0');
        $this->addSql('INSERT INTO show_user(show_id,user_id) SELECT id,userid5 FROM show WHERE userid5 <> 0');
        //drop the users from the show table
        $this->addSql('ALTER TABLE show DROP userid1;');
        $this->addSql('ALTER TABLE show DROP userid2;');
        $this->addSql('ALTER TABLE show DROP userid3;');
        $this->addSql('ALTER TABLE show DROP userid4;');
        $this->addSql('ALTER TABLE show DROP userid5;');

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->addSql('ALTER TABLE show RENAME TO shows;');
        $this->addSql('ALTER TABLE shows ADD userid1 BIGINT unsigned NOT NULL');
        $this->addSql('ALTER TABLE shows ADD userid2 BIGINT unsigned NOT NULL');
        $this->addSql('ALTER TABLE shows ADD userid3 BIGINT unsigned NOT NULL');
        $this->addSql('ALTER TABLE shows ADD userid4 BIGINT unsigned NOT NULL');
        $this->addSql('ALTER TABLE shows ADD userid5 BIGINT unsigned NOT NULL');

    }
}
