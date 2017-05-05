<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170421154216 extends AbstractMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql(file_get_contents(__DIR__ . '/Version20170421154216.sql'));

        //GENRES MIGRATION --------------------------------------------------------------------------------------------------------------
        $this->addSql('ALTER TABLE genres ADD id BIGINT AUTO_INCREMENT NOT NULL, ADD PRIMARY KEY (id)');
        $this->addSql('CREATE UNIQUE INDEX genres_id_uindex ON genres (id)');

        //USER MIGRATION --------------------------------------------------------------------------------------------------------------
        $this->addSql('ALTER TABLE users RENAME TO user;');
        $this->addSql('ALTER TABLE user ENGINE=INNODB');
        $this->addSql('ALTER TABLE user CHANGE userid id BIGINT(20) unsigned NOT NULL;');

        $this->addSql('ALTER TABLE user ADD confirmed TINYINT');
        $this->addSql('ALTER TABLE user ADD confirmation_token VARCHAR(30)');
        $this->addSql('ALTER TABLE user ADD username VARCHAR(30)');

        $this->addSql('ALTER TABLE user DROP fname');
        $this->addSql('ALTER TABLE user DROP lname');
        $this->addSql('ALTER TABLE user DROP verifycode');

        $this->addSql('ALTER TABLE user MODIFY fbid BIGINT(20) unsigned');
        $this->addSql('ALTER TABLE user MODIFY phone VARCHAR(30)');
        $this->addSql('ALTER TABLE user MODIFY staffgroup VARCHAR(200)');
        $this->addSql('ALTER TABLE user MODIFY staffposition VARCHAR(200)');
        $this->addSql('ALTER TABLE user MODIFY staffemail VARCHAR(200)');
        $this->addSql('ALTER TABLE user MODIFY seasons VARCHAR(140);');
        $this->addSql('ALTER TABLE user MODIFY lastlogin DATETIME;');
        $this->addSql('ALTER TABLE user MODIFY djname VARCHAR(120);');
        $this->addSql('ALTER TABLE user MODIFY gender VARCHAR(100);');
        $this->addSql('ALTER TABLE user MODIFY lastip VARCHAR(30);');
        $this->addSql('ALTER TABLE user MODIFY password VARCHAR(255) NOT NULL;');
        $this->addSql('CREATE UNIQUE INDEX user_email_uindex ON user (email)');
        $this->addSql('CREATE UNIQUE INDEX user_username_uindex ON user (username)');

        $this->addSql('UPDATE user set confirmed=1');

        $this->addSql('CREATE TABLE user_role
        (
            id BIGINT(20) unsigned NOT NULL PRIMARY KEY AUTO_INCREMENT,
            user_id BIGINT(20) unsigned NOT NULL,
            role VARCHAR(30) NOT NULL,
            CONSTRAINT role_users_id_fk FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE
        );');
        $this->addSql('INSERT INTO user_role(user_id,role) SELECT id,"ROLE_USER" FROM  user ');
        $this->addSql('ALTER TABLE user DROP type');


        //shows --------------------------------------------------------------------------------------------------------------
        $this->addSql('ALTER TABLE shows RENAME TO `show`;');

        $this->addSql('ALTER TABLE `show` ENGINE=INNODB');

        $this->addSql('ALTER TABLE `show` CHANGE showid id BIGINT(20) unsigned NOT NULL AUTO_INCREMENT;');
        $this->addSql('ALTER TABLE `show` ADD update_on DATETIME NOT NULL;');

        $this->addSql('ALTER TABLE `show` MODIFY description BLOB NOT NULL;');

        $this->addSql('ALTER TABLE `show` CHANGE showname name VARCHAR(100) NOT NULL;');
        $this->addSql('ALTER TABLE `show` CHANGE createdon created_on DATETIME NOT NULL;');
        $this->addSql('ALTER TABLE `show` CHANGE ranking score INT NOT NULL;');
        $this->addSql('ALTER TABLE `show` CHANGE clean profanity TINYINT(1) NOT NULL DEFAULT "0";');
        $this->addSql('ALTER TABLE `show` CHANGE attendanceoptional attendance_optional TINYINT(1) NOT NULL DEFAULT "0";');

        $this->addSql('ALTER TABLE `show` DROP showtime;');
        $this->addSql('ALTER TABLE `show` DROP seasons;');
        $this->addSql('ALTER TABLE `show` DROP genre;');
        $this->addSql('ALTER TABLE `show` DROP musictalk;');
        $this->addSql('ALTER TABLE `show` DROP timestamp2;');
        $this->addSql('ALTER TABLE `show` DROP turntables;');
        $this->addSql('ALTER TABLE `show` DROP podcastcategory;');
        $this->addSql('ALTER TABLE `show` DROP link;');
        $this->addSql('ALTER TABLE `show` DROP elevation;');
        $this->addSql('ALTER TABLE `show` DROP swing;');
        $this->addSql('ALTER TABLE `show` DROP podcastenabled;');
        $this->addSql('ALTER TABLE `show` DROP status;');
        $this->addSql('ALTER TABLE `show` DROP app_differentiate;');
        $this->addSql('ALTER TABLE `show` DROP app_promote;');
        $this->addSql('ALTER TABLE `show` DROP app_timeline;');
        $this->addSql('ALTER TABLE `show` DROP app_giveaway;');
        $this->addSql('ALTER TABLE `show` DROP app_speaking;');
        $this->addSql('ALTER TABLE `show` DROP app_equipment;');
        $this->addSql('ALTER TABLE `show` DROP app_prepare;');
        $this->addSql('ALTER TABLE `show` DROP app_examples;');
        $this->addSql('ALTER TABLE `show` DROP availability;');
        $this->addSql('ALTER TABLE `show` DROP availabilitynotes;');
        $this->addSql('ALTER TABLE `show` DROP revisionkey;');


        $this->addSql('CREATE TABLE show_user
        (
            id BIGINT(20) unsigned NOT NULL PRIMARY KEY AUTO_INCREMENT,
            show_id BIGINT unsigned NOT NULL,
            user_id BIGINT unsigned NOT NULL,
            CONSTRAINT show_user_users_id_fk FOREIGN KEY (user_id) REFERENCES user (id),
            CONSTRAINT show_user_shows_id_fk FOREIGN KEY (show_id) REFERENCES `show` (id)
        );');

        //move user ids to the show_user table
        $this->addSql('INSERT INTO show_user(show_id,user_id) SELECT id,userid1 FROM `show` WHERE userid1 <> 0');
        $this->addSql('INSERT INTO show_user(show_id,user_id) SELECT id,userid2 FROM `show` WHERE userid2 <> 0');
        $this->addSql('INSERT INTO show_user(show_id,user_id) SELECT id,userid3 FROM `show` WHERE userid3 <> 0');
        $this->addSql('INSERT INTO show_user(show_id,user_id) SELECT id,userid4 FROM `show` WHERE userid4 <> 0');
        $this->addSql('INSERT INTO show_user(show_id,user_id) SELECT id,userid5 FROM `show` WHERE userid5 <> 0');
        //drop the users from the show table
        $this->addSql('ALTER TABLE `show` DROP userid1;');
        $this->addSql('ALTER TABLE `show` DROP userid2;');
        $this->addSql('ALTER TABLE `show` DROP userid3;');
        $this->addSql('ALTER TABLE `show` DROP userid4;');
        $this->addSql('ALTER TABLE `show` DROP userid5;');

    }


    public function postUp(Schema $schema)
    {
        // upgrade user passwords
        $batch_size = 20;
        $i = 0;

        $encoder = $this->container->get('security.password_encoder');

        $em =$this->container->get('doctrine')->getEntityManager();
        $repo = $em->getRepository('AppBundle:User');
        $qb = $repo->createQueryBuilder('i');

        $it = $qb->getQuery()->iterate();
        foreach ($it as $row) {
            /** @var User $ent */
            $ent = $row[0];

            echo $ent->getEmail() . "\n";


            $password = Util::decrypt($ent->getPassword());
            $p = $encoder->encodePassword($ent, $password);
            $ent->setPassword($p);
            if (($i % $batch_size) === 0) {
                $em->flush(); // Executes all updates.
                $em->clear(); // Detaches all objects from Doctrine!
            }
            ++$i;
        }
        $em->flush();

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('DROP TABLE `aliases`');
        $this->addSql('DROP TABLE `alterations`');
        $this->addSql('DROP TABLE `announcements`');
        $this->addSql('DROP TABLE `attendance`');
        $this->addSql('DROP TABLE `awards`');
        $this->addSql('DROP TABLE `blacklist`');
        $this->addSql('DROP TABLE `calendar`');
        $this->addSql('DROP TABLE `downtime`');
        $this->addSql('DROP TABLE `emaillists`');
        $this->addSql('DROP TABLE `errors`');
        $this->addSql('DROP TABLE `evals`');
        $this->addSql('DROP TABLE `eventpics`');
        $this->addSql('DROP TABLE `events`');
        $this->addSql('DROP TABLE `features`');
        $this->addSql('DROP TABLE `feed`');
        $this->addSql('DROP TABLE `finalexam`');
        $this->addSql('DROP TABLE `genrecontent`');
        $this->addSql('DROP TABLE `geoip`');
        $this->addSql('DROP TABLE `giveaways`');
        $this->addSql('DROP TABLE `giveawayshows`');
        $this->addSql('DROP TABLE `listens`');
        $this->addSql('DROP TABLE `livechat`');
        $this->addSql('DROP TABLE `locations`');
        $this->addSql('DROP TABLE `messages`');
        $this->addSql('DROP TABLE `news`');
        $this->addSql('DROP TABLE `notifications`');
        $this->addSql('DROP TABLE `nowplaying`');
        $this->addSql('DROP TABLE `prefs`');
        $this->addSql('DROP TABLE `promos`');
        $this->addSql('DROP TABLE `quizes`');
        $this->addSql('DROP TABLE `quizquestions`');
        $this->addSql('DROP TABLE `schedule`');
        $this->addSql('DROP TABLE `shows`');
        $this->addSql('DROP TABLE `sports`');
        $this->addSql('DROP TABLE `staff`');
        $this->addSql('DROP TABLE `stats`');
        $this->addSql('DROP TABLE `strikes`');
        $this->addSql('DROP TABLE `suspendedloginattempts`');
        $this->addSql('DROP TABLE `tags`');
        $this->addSql('DROP TABLE `tracks`');
        $this->addSql('DROP TABLE `user`');
        $this->addSql('DROP TABLE `attendance_events`');
        $this->addSql('DROP TABLE `genres`');
        $this->addSql('DROP TABLE `geoip_old`');
        $this->addSql('DROP TABLE `grade_structure`');
        $this->addSql('DROP TABLE `grade_values`');
        $this->addSql('DROP TABLE `livechat_contacts`');
        $this->addSql('DROP TABLE `mp3s`');
        $this->addSql('DROP TABLE `schedule_temp`');
        $this->addSql('DROP TABLE `show_aliases`');
        $this->addSql('DROP TABLE `show_sitins`');
        $this->addSql('DROP TABLE `staff_log`');
        $this->addSql('DROP TABLE `training_signups`');
        $this->addSql('DROP TABLE `training_slots`');

        $this->addSql('DROP VIEW `v_features_active`');
        $this->addSql('DROP VIEW `v_listener_map`');
        $this->addSql('DROP VIEW `v_livechat`');
        $this->addSql('DROP VIEW `v_news_active`');
        $this->addSql('DROP VIEW `v_training_signups`');
        $this->addSql('DROP VIEW `v_training_slots`');


    }




}
