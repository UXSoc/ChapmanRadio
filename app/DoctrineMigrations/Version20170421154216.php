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
        $this->addSql(file_get_contents(__DIR__ . '/Version20170421154216.sql'));

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
        $this->addSql('DROP TABLE `users`');
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
