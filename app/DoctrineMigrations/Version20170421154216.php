<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170421154216 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE errors CHANGE timestamp timestamp DATETIME NOT NULL');
        $this->addSql('ALTER TABLE downtime CHANGE datetime datetime DATETIME NOT NULL, CHANGE icecastisdown icecastisdown TINYINT(1) NOT NULL, CHANGE chapmanradioisdown chapmanradioisdown TINYINT(1) NOT NULL, CHANGE chapmanradiolowqualityisdown chapmanradiolowqualityisdown TINYINT(1) NOT NULL, CHANGE notified notified TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE prefs CHANGE `key` `key` VARCHAR(100) NOT NULL');
        $this->addSql('ALTER TABLE suspendedloginattempts CHANGE attemptid attemptid BIGINT AUTO_INCREMENT NOT NULL, CHANGE userid userid BIGINT NOT NULL, CHANGE timestamp timestamp BIGINT NOT NULL');
        $this->addSql('ALTER TABLE aliases CHANGE path path VARCHAR(30) NOT NULL, CHANGE timestamp timestamp DATETIME NOT NULL, CHANGE expires expires DATETIME NOT NULL');
        $this->addSql('ALTER TABLE locations CHANGE location_id location_id BIGINT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE shows CHANGE showid showid BIGINT AUTO_INCREMENT NOT NULL, CHANGE userid1 userid1 BIGINT NOT NULL, CHANGE userid2 userid2 BIGINT NOT NULL, CHANGE userid3 userid3 BIGINT NOT NULL, CHANGE userid4 userid4 BIGINT NOT NULL, CHANGE userid5 userid5 BIGINT NOT NULL, CHANGE timestamp2 timestamp2 BIGINT NOT NULL, CHANGE explicit explicit TINYINT(1) NOT NULL, CHANGE elevation elevation SMALLINT NOT NULL, CHANGE swing swing TINYINT(1) NOT NULL, CHANGE ranking ranking TINYINT(1) NOT NULL, CHANGE podcastenabled podcastenabled TINYINT(1) NOT NULL, CHANGE clean clean TINYINT(1) NOT NULL, CHANGE attendanceoptional attendanceoptional TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE training_slots CHANGE trainingslot_id trainingslot_id BIGINT AUTO_INCREMENT NOT NULL, CHANGE trainingslot_season trainingslot_season VARCHAR(6) NOT NULL, CHANGE trainingslot_staffid trainingslot_staffid BIGINT NOT NULL');
        $this->addSql('ALTER TABLE geoip_old CHANGE geoipid geoipid BIGINT AUTO_INCREMENT NOT NULL, CHANGE ip1 ip1 INT NOT NULL, CHANGE ip2 ip2 INT NOT NULL, CHANGE ip3 ip3 INT NOT NULL, CHANGE ip4 ip4 INT NOT NULL, CHANGE total total INT NOT NULL');
        $this->addSql('ALTER TABLE schedule CHANGE hour hour TINYINT(1) NOT NULL, CHANGE mon mon VARCHAR(57) NOT NULL, CHANGE tue tue VARCHAR(57) NOT NULL, CHANGE wed wed VARCHAR(57) NOT NULL, CHANGE thu thu VARCHAR(57) NOT NULL, CHANGE fri fri VARCHAR(57) NOT NULL, CHANGE sat sat VARCHAR(57) NOT NULL, CHANGE sun sun VARCHAR(57) NOT NULL');
        $this->addSql('ALTER TABLE training_signups CHANGE trainingsignup_id trainingsignup_id BIGINT AUTO_INCREMENT NOT NULL, CHANGE trainingsignup_slot trainingsignup_slot BIGINT NOT NULL, CHANGE trainingsignup_userid trainingsignup_userid BIGINT NOT NULL, CHANGE trainingsignup_present trainingsignup_present VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE attendance CHANGE attendanceid attendanceid BIGINT AUTO_INCREMENT NOT NULL, CHANGE timestamp timestamp BIGINT NOT NULL, CHANGE showid showid BIGINT NOT NULL, CHANGE userid userid BIGINT NOT NULL, CHANGE type type VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE alterations CHANGE alterationid alterationid BIGINT AUTO_INCREMENT NOT NULL, CHANGE starttimestamp starttimestamp BIGINT NOT NULL, CHANGE endtimestamp endtimestamp BIGINT NOT NULL, CHANGE showid showid BIGINT NOT NULL, CHANGE alteredby alteredby BIGINT NOT NULL');
        $this->addSql('ALTER TABLE awards CHANGE awardid awardid BIGINT AUTO_INCREMENT NOT NULL, CHANGE type type VARCHAR(75) NOT NULL, CHANGE showid showid BIGINT NOT NULL');
        $this->addSql('ALTER TABLE staff CHANGE userid userid BIGINT NOT NULL');
        $this->addSql('ALTER TABLE geoip CHANGE geoip_ip geoip_ip VARBINARY(255) NOT NULL, CHANGE geoip_countrycode geoip_countrycode VARCHAR(2) NOT NULL');
        $this->addSql('ALTER TABLE events CHANGE eventid eventid BIGINT AUTO_INCREMENT NOT NULL, CHANGE timestamp timestamp BIGINT NOT NULL, CHANGE active active TINYINT(1) NOT NULL, CHANGE primaryeventpicid primaryeventpicid BIGINT NOT NULL');
        $this->addSql('ALTER TABLE features CHANGE feature_priority feature_priority BIGINT NOT NULL, CHANGE feature_active feature_active TINYINT(1) NOT NULL, CHANGE revisionkey revisionkey VARCHAR(30) NOT NULL');
        $this->addSql('ALTER TABLE mp3s CHANGE mp3id mp3id BIGINT AUTO_INCREMENT NOT NULL, CHANGE downloads downloads INT NOT NULL, CHANGE streams streams INT NOT NULL, CHANGE podcasts podcasts INT NOT NULL, CHANGE active active TINYINT(1) NOT NULL, CHANGE clean clean TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE promos CHANGE promoid promoid BIGINT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE tags CHANGE tagid tagid BIGINT AUTO_INCREMENT NOT NULL, CHANGE showid showid BIGINT NOT NULL');
        $this->addSql('ALTER TABLE strikes CHANGE strikeid strikeid BIGINT AUTO_INCREMENT NOT NULL, CHANGE userid userid BIGINT NOT NULL, CHANGE emailsent emailsent TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE messages CHANGE created created DATETIME NOT NULL');
        $this->addSql('ALTER TABLE emaillists CHANGE emaillistid emaillistid BIGINT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE users CHANGE userid userid BIGINT AUTO_INCREMENT NOT NULL, CHANGE fbid fbid BIGINT NOT NULL, CHANGE petpreference petpreference VARCHAR(255) NOT NULL, CHANGE confirmnewsletter confirmnewsletter TINYINT(1) NOT NULL, CHANGE workshoprequired workshoprequired TINYINT(1) NOT NULL, CHANGE suspended suspended TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE livechat_contacts CHANGE contactkey contactkey VARCHAR(20) NOT NULL, CHANGE contactupdated contactupdated DATETIME NOT NULL');
        $this->addSql('ALTER TABLE listens CHANGE listen_id listen_id BIGINT AUTO_INCREMENT NOT NULL, CHANGE source source VARCHAR(255) NOT NULL, CHANGE ipaddr ipaddr VARBINARY(255) NOT NULL');
        $this->addSql('ALTER TABLE tracks CHANGE track_id track_id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE finalexam CHANGE exam_id exam_id BIGINT AUTO_INCREMENT NOT NULL, CHANGE exam_user exam_user BIGINT NOT NULL, CHANGE exam_mp3 exam_mp3 BIGINT NOT NULL, CHANGE exam_created exam_created DATETIME NOT NULL');
        $this->addSql('ALTER TABLE genres ADD id BIGINT AUTO_INCREMENT NOT NULL, CHANGE hour hour TINYINT(1) NOT NULL, ADD PRIMARY KEY (id)');
        $this->addSql('CREATE UNIQUE INDEX genres_id_uindex ON genres (id)');
        $this->addSql('ALTER TABLE quizquestions CHANGE quizquestionid quizquestionid BIGINT AUTO_INCREMENT NOT NULL, CHANGE createdby createdby BIGINT NOT NULL, CHANGE active active TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE giveaways CHANGE giveawayid giveawayid BIGINT AUTO_INCREMENT NOT NULL, CHANGE hometext hometext VARBINARY(255) NOT NULL, CHANGE active active TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE announcements CHANGE announcementid announcementid BIGINT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE giveawayshows CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE giveawayid giveawayid INT NOT NULL, CHANGE showid showid INT NOT NULL, CHANGE timestamp timestamp INT NOT NULL');
        $this->addSql('ALTER TABLE genrecontent CHANGE genre genre VARCHAR(40) NOT NULL, CHANGE staffid staffid BIGINT NOT NULL');
        $this->addSql('ALTER TABLE calendar CHANGE calendar_type calendar_type VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE sports CHANGE id id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE feed CHANGE guid guid INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE attendance_events CHANGE timestamp timestamp BIGINT AUTO_INCREMENT NOT NULL, CHANGE season season VARCHAR(6) NOT NULL');
        $this->addSql('ALTER TABLE evals CHANGE evalid evalid BIGINT AUTO_INCREMENT NOT NULL, CHANGE userid userid BIGINT NOT NULL, CHANGE showid showid BIGINT NOT NULL, CHANGE timestamp timestamp BIGINT NOT NULL, CHANGE postedtimestamp postedtimestamp BIGINT NOT NULL, CHANGE live live TINYINT(1) NOT NULL, CHANGE active active TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE quizes CHANGE quizid quizid BIGINT AUTO_INCREMENT NOT NULL, CHANGE userid userid BIGINT NOT NULL, CHANGE startedon startedon BIGINT NOT NULL, CHANGE completed completed TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE notifications CHANGE notificationid notificationid BIGINT AUTO_INCREMENT NOT NULL, CHANGE timestamp timestamp BIGINT NOT NULL');
        $this->addSql('ALTER TABLE show_sitins CHANGE season season VARCHAR(6) NOT NULL');
        $this->addSql('ALTER TABLE eventpics CHANGE eventpicid eventpicid BIGINT AUTO_INCREMENT NOT NULL, CHANGE eventid eventid BIGINT NOT NULL');
        $this->addSql('ALTER TABLE stats CHANGE datetime datetime DATETIME NOT NULL, CHANGE showid showid BIGINT NOT NULL, CHANGE chapmanradio chapmanradio SMALLINT NOT NULL, CHANGE chapmanradiolowquality chapmanradiolowquality SMALLINT NOT NULL, CHANGE sports sports SMALLINT NOT NULL');
        $this->addSql('ALTER TABLE blacklist CHANGE email email VARCHAR(300) NOT NULL');
        $this->addSql('ALTER TABLE schedule_temp CHANGE schedule_id schedule_id BIGINT AUTO_INCREMENT NOT NULL, CHANGE schedule_season schedule_season VARCHAR(6) NOT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE aliases CHANGE path path VARCHAR(30) NOT NULL COLLATE utf8_general_ci, CHANGE timestamp timestamp DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE expires expires DATETIME DEFAULT \'0000-00-00 00:00:00\' NOT NULL');
        $this->addSql('ALTER TABLE alterations CHANGE alterationid alterationid BIGINT UNSIGNED AUTO_INCREMENT NOT NULL, CHANGE starttimestamp starttimestamp BIGINT UNSIGNED NOT NULL, CHANGE endtimestamp endtimestamp BIGINT UNSIGNED NOT NULL, CHANGE showid showid BIGINT UNSIGNED NOT NULL, CHANGE alteredby alteredby BIGINT UNSIGNED NOT NULL');
        $this->addSql('ALTER TABLE announcements CHANGE announcementid announcementid BIGINT UNSIGNED AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE attendance CHANGE attendanceid attendanceid BIGINT UNSIGNED AUTO_INCREMENT NOT NULL, CHANGE timestamp timestamp BIGINT UNSIGNED NOT NULL, CHANGE showid showid BIGINT UNSIGNED NOT NULL, CHANGE userid userid BIGINT UNSIGNED NOT NULL, CHANGE type type VARCHAR(255) DEFAULT \'show\' NOT NULL COLLATE utf8_general_ci');
        $this->addSql('ALTER TABLE attendance_events CHANGE timestamp timestamp BIGINT NOT NULL, CHANGE season season CHAR(6) NOT NULL COLLATE utf8_general_ci');
        $this->addSql('ALTER TABLE awards CHANGE awardid awardid BIGINT UNSIGNED AUTO_INCREMENT NOT NULL, CHANGE type type VARCHAR(75) DEFAULT \'showoftheweek\' NOT NULL COLLATE utf8_general_ci, CHANGE showid showid BIGINT UNSIGNED NOT NULL');
        $this->addSql('ALTER TABLE blacklist CHANGE email email VARCHAR(300) NOT NULL COLLATE utf8_general_ci');
        $this->addSql('ALTER TABLE calendar CHANGE calendar_type calendar_type VARCHAR(255) DEFAULT \'public\' NOT NULL COLLATE utf8_general_ci');
        $this->addSql('ALTER TABLE downtime CHANGE datetime datetime DATETIME NOT NULL, CHANGE icecastisdown icecastisdown TINYINT(1) DEFAULT \'0\' NOT NULL, CHANGE chapmanradioisdown chapmanradioisdown TINYINT(1) DEFAULT \'0\' NOT NULL, CHANGE chapmanradiolowqualityisdown chapmanradiolowqualityisdown TINYINT(1) DEFAULT \'0\' NOT NULL, CHANGE notified notified TINYINT(1) DEFAULT \'0\' NOT NULL');
        $this->addSql('ALTER TABLE emaillists CHANGE emaillistid emaillistid BIGINT UNSIGNED AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE errors CHANGE timestamp timestamp DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE evals CHANGE evalid evalid BIGINT UNSIGNED AUTO_INCREMENT NOT NULL, CHANGE userid userid BIGINT UNSIGNED NOT NULL, CHANGE showid showid BIGINT UNSIGNED NOT NULL, CHANGE timestamp timestamp BIGINT UNSIGNED NOT NULL, CHANGE postedtimestamp postedtimestamp BIGINT UNSIGNED NOT NULL, CHANGE live live TINYINT(1) DEFAULT \'1\' NOT NULL, CHANGE active active TINYINT(1) DEFAULT \'1\' NOT NULL');
        $this->addSql('ALTER TABLE eventpics CHANGE eventpicid eventpicid BIGINT UNSIGNED AUTO_INCREMENT NOT NULL, CHANGE eventid eventid BIGINT UNSIGNED NOT NULL');
        $this->addSql('ALTER TABLE events CHANGE eventid eventid BIGINT UNSIGNED AUTO_INCREMENT NOT NULL, CHANGE timestamp timestamp BIGINT UNSIGNED NOT NULL, CHANGE active active TINYINT(1) DEFAULT \'0\' NOT NULL, CHANGE primaryeventpicid primaryeventpicid BIGINT UNSIGNED DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE features CHANGE feature_priority feature_priority BIGINT DEFAULT 0 NOT NULL, CHANGE feature_active feature_active TINYINT(1) DEFAULT \'0\' NOT NULL, CHANGE revisionkey revisionkey VARCHAR(30) DEFAULT \'\' NOT NULL COLLATE utf8_general_ci');
        $this->addSql('ALTER TABLE feed CHANGE guid guid INT NOT NULL');
        $this->addSql('ALTER TABLE finalexam CHANGE exam_id exam_id BIGINT UNSIGNED AUTO_INCREMENT NOT NULL, CHANGE exam_user exam_user BIGINT UNSIGNED NOT NULL, CHANGE exam_mp3 exam_mp3 BIGINT UNSIGNED NOT NULL, CHANGE exam_created exam_created DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE genrecontent CHANGE genre genre VARCHAR(40) NOT NULL COLLATE utf8_general_ci, CHANGE staffid staffid BIGINT UNSIGNED NOT NULL');
        $this->addSql('ALTER TABLE genres MODIFY id BIGINT NOT NULL');
        $this->addSql('ALTER TABLE genres DROP PRIMARY KEY');
        $this->addSql('DROP INDEX genres_id_uindex ON genres');
        $this->addSql('ALTER TABLE genres DROP id, CHANGE hour hour TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE geoip CHANGE geoip_ip geoip_ip VARBINARY(16) NOT NULL, CHANGE geoip_countrycode geoip_countrycode CHAR(2) NOT NULL COLLATE latin1_swedish_ci');
        $this->addSql('ALTER TABLE geoip_old CHANGE geoipid geoipid BIGINT UNSIGNED AUTO_INCREMENT NOT NULL, CHANGE ip1 ip1 INT UNSIGNED NOT NULL, CHANGE ip2 ip2 INT UNSIGNED NOT NULL, CHANGE ip3 ip3 INT UNSIGNED NOT NULL, CHANGE ip4 ip4 INT UNSIGNED NOT NULL, CHANGE total total INT DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE giveaways CHANGE giveawayid giveawayid BIGINT UNSIGNED AUTO_INCREMENT NOT NULL, CHANGE hometext hometext VARBINARY(600) NOT NULL, CHANGE active active TINYINT(1) DEFAULT \'0\' NOT NULL');
        $this->addSql('ALTER TABLE giveawayshows CHANGE id id INT UNSIGNED AUTO_INCREMENT NOT NULL, CHANGE giveawayid giveawayid INT UNSIGNED NOT NULL, CHANGE showid showid INT UNSIGNED NOT NULL, CHANGE timestamp timestamp INT UNSIGNED NOT NULL');
        $this->addSql('ALTER TABLE listens CHANGE listen_id listen_id BIGINT UNSIGNED AUTO_INCREMENT NOT NULL, CHANGE source source VARCHAR(255) DEFAULT \'unknown\' NOT NULL COLLATE utf8_general_ci, CHANGE ipaddr ipaddr VARBINARY(16) NOT NULL');
        $this->addSql('ALTER TABLE livechat_contacts CHANGE contactkey contactkey VARCHAR(20) NOT NULL COLLATE utf8_general_ci, CHANGE contactupdated contactupdated DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE locations CHANGE location_id location_id BIGINT UNSIGNED AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE messages CHANGE created created DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE mp3s CHANGE mp3id mp3id BIGINT UNSIGNED AUTO_INCREMENT NOT NULL, CHANGE downloads downloads INT UNSIGNED DEFAULT 0 NOT NULL, CHANGE streams streams INT UNSIGNED DEFAULT 0 NOT NULL, CHANGE podcasts podcasts INT UNSIGNED DEFAULT 0 NOT NULL, CHANGE active active TINYINT(1) DEFAULT \'1\' NOT NULL, CHANGE clean clean TINYINT(1) DEFAULT \'0\' NOT NULL');
        $this->addSql('ALTER TABLE notifications CHANGE notificationid notificationid BIGINT UNSIGNED AUTO_INCREMENT NOT NULL, CHANGE timestamp timestamp BIGINT UNSIGNED NOT NULL');
        $this->addSql('ALTER TABLE prefs CHANGE `key` `key` VARCHAR(100) NOT NULL COLLATE utf8_general_ci');
        $this->addSql('ALTER TABLE promos CHANGE promoid promoid BIGINT UNSIGNED AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE quizes CHANGE quizid quizid BIGINT UNSIGNED AUTO_INCREMENT NOT NULL, CHANGE userid userid BIGINT UNSIGNED NOT NULL, CHANGE startedon startedon BIGINT UNSIGNED NOT NULL, CHANGE completed completed TINYINT(1) DEFAULT \'0\' NOT NULL');
        $this->addSql('ALTER TABLE quizquestions CHANGE quizquestionid quizquestionid BIGINT UNSIGNED AUTO_INCREMENT NOT NULL, CHANGE createdby createdby BIGINT UNSIGNED NOT NULL, CHANGE active active TINYINT(1) DEFAULT \'1\' NOT NULL');
        $this->addSql('ALTER TABLE schedule CHANGE hour hour TINYINT(1) NOT NULL, CHANGE mon mon VARCHAR(57) DEFAULT \',\' NOT NULL COLLATE utf8_general_ci, CHANGE tue tue VARCHAR(57) DEFAULT \',\' NOT NULL COLLATE utf8_general_ci, CHANGE wed wed VARCHAR(57) DEFAULT \',\' NOT NULL COLLATE utf8_general_ci, CHANGE thu thu VARCHAR(57) DEFAULT \',\' NOT NULL COLLATE utf8_general_ci, CHANGE fri fri VARCHAR(57) DEFAULT \',\' NOT NULL COLLATE utf8_general_ci, CHANGE sat sat VARCHAR(57) DEFAULT \',\' NOT NULL COLLATE utf8_general_ci, CHANGE sun sun VARCHAR(57) DEFAULT \',\' NOT NULL COLLATE utf8_general_ci');
        $this->addSql('ALTER TABLE schedule_temp CHANGE schedule_id schedule_id BIGINT UNSIGNED AUTO_INCREMENT NOT NULL, CHANGE schedule_season schedule_season CHAR(6) NOT NULL COLLATE latin1_swedish_ci');
        $this->addSql('ALTER TABLE show_sitins CHANGE season season CHAR(6) NOT NULL COLLATE utf8_general_ci');
        $this->addSql('ALTER TABLE shows CHANGE showid showid BIGINT UNSIGNED AUTO_INCREMENT NOT NULL, CHANGE userid1 userid1 BIGINT UNSIGNED NOT NULL, CHANGE userid2 userid2 BIGINT UNSIGNED NOT NULL, CHANGE userid3 userid3 BIGINT UNSIGNED NOT NULL, CHANGE userid4 userid4 BIGINT UNSIGNED NOT NULL, CHANGE userid5 userid5 BIGINT UNSIGNED NOT NULL, CHANGE timestamp2 timestamp2 BIGINT UNSIGNED NOT NULL, CHANGE explicit explicit TINYINT(1) DEFAULT \'0\' NOT NULL, CHANGE elevation elevation SMALLINT DEFAULT 0 NOT NULL, CHANGE swing swing TINYINT(1) DEFAULT \'0\' NOT NULL, CHANGE ranking ranking TINYINT(1) NOT NULL, CHANGE podcastenabled podcastenabled TINYINT(1) DEFAULT \'0\' NOT NULL, CHANGE clean clean TINYINT(1) DEFAULT \'0\' NOT NULL, CHANGE attendanceoptional attendanceoptional TINYINT(1) DEFAULT \'0\' NOT NULL');
        $this->addSql('ALTER TABLE sports CHANGE id id INT UNSIGNED AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE staff CHANGE userid userid BIGINT UNSIGNED NOT NULL');
        $this->addSql('ALTER TABLE stats CHANGE datetime datetime DATETIME NOT NULL, CHANGE showid showid BIGINT DEFAULT 0 NOT NULL, CHANGE chapmanradio chapmanradio SMALLINT UNSIGNED DEFAULT 0 NOT NULL, CHANGE chapmanradiolowquality chapmanradiolowquality SMALLINT UNSIGNED DEFAULT 0 NOT NULL, CHANGE sports sports SMALLINT UNSIGNED DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE strikes CHANGE strikeid strikeid BIGINT UNSIGNED AUTO_INCREMENT NOT NULL, CHANGE userid userid BIGINT UNSIGNED NOT NULL, CHANGE emailsent emailsent TINYINT(1) DEFAULT \'0\' NOT NULL');
        $this->addSql('ALTER TABLE suspendedloginattempts CHANGE attemptid attemptid BIGINT UNSIGNED AUTO_INCREMENT NOT NULL, CHANGE userid userid BIGINT UNSIGNED NOT NULL, CHANGE timestamp timestamp BIGINT UNSIGNED NOT NULL');
        $this->addSql('ALTER TABLE tags CHANGE tagid tagid BIGINT UNSIGNED AUTO_INCREMENT NOT NULL, CHANGE showid showid BIGINT UNSIGNED NOT NULL');
        $this->addSql('ALTER TABLE tracks CHANGE track_id track_id INT NOT NULL');
        $this->addSql('ALTER TABLE training_signups CHANGE trainingsignup_id trainingsignup_id BIGINT UNSIGNED AUTO_INCREMENT NOT NULL, CHANGE trainingsignup_slot trainingsignup_slot BIGINT UNSIGNED NOT NULL, CHANGE trainingsignup_userid trainingsignup_userid BIGINT UNSIGNED NOT NULL, CHANGE trainingsignup_present trainingsignup_present VARCHAR(255) DEFAULT \'0\' NOT NULL COLLATE utf8_general_ci');
        $this->addSql('ALTER TABLE training_slots CHANGE trainingslot_id trainingslot_id BIGINT UNSIGNED AUTO_INCREMENT NOT NULL, CHANGE trainingslot_season trainingslot_season CHAR(6) NOT NULL COLLATE utf8_general_ci, CHANGE trainingslot_staffid trainingslot_staffid BIGINT UNSIGNED NOT NULL');
        $this->addSql('ALTER TABLE users CHANGE userid userid BIGINT UNSIGNED AUTO_INCREMENT NOT NULL, CHANGE fbid fbid BIGINT UNSIGNED NOT NULL, CHANGE petpreference petpreference VARCHAR(255) DEFAULT \'none\' NOT NULL COLLATE utf8_general_ci, CHANGE confirmnewsletter confirmnewsletter TINYINT(1) DEFAULT \'0\' NOT NULL, CHANGE workshoprequired workshoprequired TINYINT(1) DEFAULT \'1\' NOT NULL, CHANGE suspended suspended TINYINT(1) DEFAULT \'0\' NOT NULL');
    }
}
