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

        $this->addSql('DROP VIEW IF EXISTS v_training_slots RESTRICT;');
        $this->addSql('DROP VIEW IF EXISTS v_training_signups RESTRICT;');
        $this->addSql('DROP VIEW IF EXISTS v_news_active RESTRICT;');
        $this->addSql('DROP VIEW IF EXISTS v_livechat RESTRICT;');
        $this->addSql('DROP VIEW IF EXISTS v_listener_map RESTRICT;');
        $this->addSql('DROP VIEW IF EXISTS v_features_active RESTRICT;');

        $this->addSql('ALTER TABLE attendance RENAME TO attendances;');
        $this->addSql(file_get_contents(__DIR__ . '/Version20170421154216_1.sql'));

//
//
//        $this->addSql('create table attendance
//        (
//            id bigint not null auto_increment
//                primary key,
//            event_id bigint null,
//            status varchar(20) null,
//            late time null,
//            strike_id bigint null,
//            created_on datetime null
//        );');
//        $this->addSql('create index attendance_event_id_fk
//	on attendance (event_id)
//    ;');
//        $this->addSql('create index attendance_strike_id_fk
//	on attendance (strike_id)
//    ;');
//        $this->addSql('create table blog
//    (
//        id bigint not null auto_increment
//		primary key,
//	author_id bigint null,
//	created_at datetime null,
//	updated_at datetime null,
//	category varchar(60) null,
//	post_exceprt text null,
//	status varchar(40) null,
//	is_pinned tinyint null,
//	content blob null
//)
//;');
//        $this->addSql('create index blog_user_id_fk
//	on blog (author_id)
//;');
//        $this->addSql('create table blog_comment
//(
//	id bigint not null auto_increment
//		primary key,
//	blog_id bigint not null,
//	comment_id bigint not null,
//	constraint blog_comment_blog_id_fk
//		foreign key (blog_id) references chapman_radio.blog (id)
//)
//;');
//        $this->addSql('create index blog_comment_blog_id_fk
//	on blog_comment (blog_id)
//;');
//        $this->addSql('create index blog_comment_comment_id_fk
//	on blog_comment (comment_id)
//;');
//        $this->addSql('create table blog_image
//(
//	id bigint not null auto_increment
//		primary key,
//	blog_id bigint null,
//	image_id bigint null,
//	constraint blog_image_blog_id_fk
//		foreign key (blog_id) references chapman_radio.blog (id)
//)
//;');
//        $this->addSql('create index blog_image_blog_id_fk
//	on blog_image (blog_id)
//;');
//        $this->addSql('create index blog_image_image_id_fk
//	on blog_image (image_id)
//;');
//        $this->addSql('create table comment
//(
//	id bigint not null auto_increment
//		primary key,
//	user_id bigint not null,
//	created_at datetime null,
//	update_at datetime null,
//	content text null,
//	comment_id bigint null,
//	constraint comment_comment_id_id_fk
//		foreign key (comment_id) references chapman_radio.comment (id)
//)
//;');
//        $this->addSql('create index comment_user_id_fk
//	on comment (user_id)
//    ;');
//        $this->addSql('create index comment_comment_id_id_fk
//	on comment (comment_id)
//    ;');
//        $this->addSql('alter table blog_comment
//	add constraint blog_comment_comment_id_fk
//		foreign key (comment_id) references chapman_radio.comment (id)
//    ;');
//        $this->addSql('create table dj
//    (
//        id bigint not null auto_increment
//		primary key,
//	user_id bigint not null,
//	description blob null,
//	strike_count int null,
//	attend_workshop tinyint null,
//	constraint dj_user_id_uindex
//		unique (user_id)
//)
//;');
//        $this->addSql('create table dj_image
//    (
//        id bigint not null auto_increment
//		primary key,
//	dj_id bigint not null,
//	image_id bigint not null,
//	constraint dj_image_dj_id_fk
//		foreign key (dj_id) references chapman_radio.dj (id)
//)
//;');
//        $this->addSql('create index dj_image_dj_id_fk
//	on dj_image (dj_id)
//    ;');
//        $this->addSql('create index dj_image_image_id_fk
//	on dj_image (image_id)
//    ;');
//        $this->addSql('create table event
//    (
//        id bigint not null
//		primary key,
//	show_schedule_id bigint null,
//	start datetime null,
//	end datetime null,
//	show_id bigint null,
//	constraint event_id_uindex
//		unique (id)
//)
//;');
//        $this->addSql('create index `_event_show_id_fk`
//	on event (show_id)
//    ;');
//        $this->addSql('create index `_event_show_schedule_id_fk`
//	on event (show_schedule_id)
//    ;');
//        $this->addSql('alter table attendance
//	add constraint attendance_event_id_fk
//		foreign key (event_id) references chapman_radio.event (id)
//    ;');
//        $this->addSql('create table image
//    (
//        id bigint not null auto_increment
//		primary key,
//	source varchar(200) not null,
//	mimetype varchar(30) null,
//	created_at datetime null
//)
//;');
//        $this->addSql('alter table blog_image
//	add constraint blog_image_image_id_fk
//		foreign key (image_id) references chapman_radio.image (id)
//;');
//        $this->addSql('alter table dj_image
//	add constraint dj_image_image_id_fk
//		foreign key (image_id) references chapman_radio.image (id)
//;');
//        $this->addSql('create table recording
//(
//	id bigint not null auto_increment primary key,
//	show_id bigint null,
//	event_id bigint null,
//	source varchar(100) null,
//	short_name varchar(80) null,
//	downloads int null,
//	created_on datetime null,
//	description text null,
//	constraint recording_event_id_fk foreign key (event_id) references chapman_radio.event (id)
//);');
//        $this->addSql('create index recording_event_id_fk
//	on recording (event_id)
//    ;');
//        $this->addSql('create index recording_show_id_fk
//	on recording (show_id)
//    ;');
//        $this->addSql('create table `show`
//(
//    id bigint not null auto_increment
//	primary key,
//	name varchar(100) not null,
//	description blob not null,
//	created_at datetime not null,
//	score int not null,
//	profanity tinyint(1) default \'0\' not null,
//	attendanceoptional tinyint(1) default \'0\' not null,
//	updated_at datetime null,
//	created_on datetime null,
//	genre varchar(80) null,
//	header_imge_id bigint null,
//	strike_count int null,
//	suspended tinyint null,
//	enable_comments tinyint default \'0\' null
//)
//;');
//        $this->addSql('alter table event
//	add constraint _event_show_id_fk
//		foreign key (show_id) references chapman_radio.`show` (id)
//    ;');
//        $this->addSql('alter table recording
//	add constraint recording_show_id_fk
//		foreign key (show_id) references chapman_radio.`show` (id)
//    ;');
//        $this->addSql('create table show_comment
//    (
//        id bigint not null auto_increment
//		primary key,
//	show_id bigint null,
//	comment_id bigint null,
//	constraint show_comment_show_id_fk
//		foreign key (show_id) references chapman_radio.`show` (id),
//	constraint show_comment_comment_id_fk
//		foreign key (comment_id) references chapman_radio.comment (id)
//)
//;');
//        $this->addSql('create index show_comment_comment_id_fk
//	on show_comment (comment_id)
//    ;');
//        $this->addSql('create index show_comment_show_id_fk
//	on show_comment (show_id)
//    ;');
//        $this->addSql('create table show_image
//    (
//        id bigint not null auto_increment
//		primary key,
//	show_id bigint not null,
//	image_id bigint not null,
//	constraint show_image_show_id_fk
//		foreign key (show_id) references chapman_radio.`show` (id),
//	constraint show_image_image_id_fk
//		foreign key (image_id) references chapman_radio.image (id)
//)
//;');
//        $this->addSql('create index show_image_image_id_fk
//	on show_image (image_id)
//    ;');
//        $this->addSql('create index show_image_show_id_fk
//	on show_image (show_id)
//    ;');
//        $this->addSql('create table show_schedule
//    (
//        id bigint not null auto_increment
//		primary key,
//	start_time time null,
//	end_time time null,
//	show_id bigint null,
//	constraint event_show_id_fk
//		foreign key (show_id) references chapman_radio.`show` (id)
//)
//;');
//        $this->addSql('create index event_show_id_fk
//	on show_schedule (show_id)
//    ;');
//        $this->addSql('alter table event
//	add constraint _event_show_schedule_id_fk
//		foreign key (show_schedule_id) references chapman_radio.show_schedule (id)
//    ;');
//        $this->addSql('create table show_schedule_meta
//    (
//        id bigint not null auto_increment
//		primary key,
//	event_id bigint null,
//	meta_key varchar(20) null,
//	meta_value bigint null,
//	constraint event_meta_event_id_fk
//		foreign key (event_id) references chapman_radio.show_schedule (id)
//)
//;');
//        $this->addSql('create index event_meta_event_id_fk
//	on show_schedule_meta (event_id)
//    ;');
//        $this->addSql('create table show_user
//    (
//        id bigint not null auto_increment
//		primary key,
//	show_id bigint not null,
//	user_id bigint not null,
//	constraint show_user_show_id_fk
//		foreign key (show_id) references chapman_radio.`show` (id)
//)
//;');
//        $this->addSql('create index show_user_show_id_fk
//	on show_user (show_id)
//    ;');
//        $this->addSql('create index show_user_user_id_fk
//	on show_user (user_id)
//    ;');
//        $this->addSql('create table strike
//    (
//        id bigint not null
//		primary key,
//	user_id bigint null,
//	created_on datetime null,
//	reason text null,
//	email_student tinyint null,
//	type varchar(20) null,
//	constraint strike_id_uindex
//		unique (id)
//)
//;');
//        $this->addSql('create index strike_user_id_fk
//	on strike (user_id)
//;');
//        $this->addSql('alter table attendance
//	add constraint attendance_strike_id_fk
//		foreign key (strike_id) references chapman_radio.strike (id)
//;');
//        $this->addSql('create table user
//        (
//            id bigint not null auto_increment
//                primary key,
//            facebook_id bigint not null,
//            email varchar(100) not null,
//            student_id varchar(15) not null,
//            phone varchar(30) not null,
//            name varchar(120) not null,
//            last_login datetime not null,
//            password varchar(200) not null,
//            suspended tinyint(1) default \'0\' not null,
//            created_at datetime null,
//            updated_at datetime null,
//            username varchar(30) null,
//            confirmed tinyint null,
//            confirmation_token varchar(30) null
//        )
//        ;');
//        $this->addSql('alter table blog
//	add constraint blog_user_id_fk
//		foreign key (author_id) references chapman_radio.user (id)
//    ;');
//        $this->addSql('alter table comment
//	add constraint comment_user_id_fk
//		foreign key (user_id) references chapman_radio.user (id)
//    ;');
//        $this->addSql('alter table dj
//	add constraint dj_user_id_fk
//		foreign key (user_id) references chapman_radio.user (id)
//    ;');
//        $this->addSql('alter table show_user
//	add constraint show_user_user_id_fk
//		foreign key (user_id) references chapman_radio.user (id)
//    ;');
//        $this->addSql('alter table strike
//	add constraint strike_user_id_fk
//		foreign key (user_id) references chapman_radio.user (id)
//    ;');
//        $this->addSql('create table user_role
//    (
//        id bigint not null auto_increment
//		primary key,
//	user_id bigint not null,
//	role varchar(30) not null,
//	constraint role_users_id_fk
//		foreign key (user_id) references chapman_radio.user (id)
//			on delete cascade
//    )
//    ;');
//        $this->addSql('create index role_users_id_fk
//	on user_role (user_id)
//    ;');
        //RENAME TABLES ------------------------------------------------------



        $this->addSql('DROP TABLE training_slots;');
        $this->addSql('DROP TABLE training_signups;');
        $this->addSql('DROP TABLE tracks;');
        $this->addSql('DROP TABLE tags;');
        $this->addSql('DROP TABLE suspendedloginattempts;');
        $this->addSql('DROP TABLE strikes;');
        $this->addSql('DROP TABLE stats;');
        $this->addSql('DROP TABLE staff_log;');
        $this->addSql('DROP TABLE staff;');
        $this->addSql('DROP TABLE sports;');
        $this->addSql('DROP TABLE show_sitins;');
        $this->addSql('DROP TABLE show_aliases;');
        $this->addSql('DROP TABLE schedule_temp;');
        $this->addSql('DROP TABLE schedule;');
        $this->addSql('DROP TABLE quizquestions;');
        $this->addSql('DROP TABLE quizes;');
        $this->addSql('DROP TABLE promos;');
        $this->addSql('DROP TABLE prefs;');
        $this->addSql('DROP TABLE nowplaying;');
        $this->addSql('DROP TABLE notifications;');
        $this->addSql('DROP TABLE news;');
        $this->addSql('DROP TABLE mp3s;');
        $this->addSql('DROP TABLE messages;');
        $this->addSql('DROP TABLE locations;');
        $this->addSql('DROP TABLE livechat_contacts;');
        $this->addSql('DROP TABLE livechat;');
        $this->addSql('DROP TABLE listens;');
        $this->addSql('DROP TABLE grade_values;');
        $this->addSql('DROP TABLE grade_structure;');
        $this->addSql('DROP TABLE giveawayshows;');
        $this->addSql('DROP TABLE giveaways;');
        $this->addSql('DROP TABLE geoip_old;');
        $this->addSql('DROP TABLE geoip;');
        $this->addSql('DROP TABLE genres;');
        $this->addSql('DROP TABLE genrecontent;');
        $this->addSql('DROP TABLE finalexam;');
        $this->addSql('DROP TABLE feed;');
        $this->addSql('DROP TABLE features;');
        $this->addSql('DROP TABLE events;');
        $this->addSql('DROP TABLE eventpics;');
        $this->addSql('DROP TABLE evals;');
        $this->addSql('DROP TABLE errors;');
        $this->addSql('DROP TABLE emaillists;');
        $this->addSql('DROP TABLE downtime;');
        $this->addSql('DROP TABLE calendar;');
        $this->addSql('DROP TABLE blacklist;');
        $this->addSql('DROP TABLE awards;');
        $this->addSql('DROP TABLE attendance_events;');
        $this->addSql('DROP TABLE announcements;');
        $this->addSql('DROP TABLE alterations;');
        $this->addSql('DROP TABLE aliases;');
        $this->addSql('DROP TABLE `shows`');
        $this->addSql('DROP TABLE `users`');
        $this->addSql('DROP TABLE `attendances`');




    }


    public function postUp(Schema $schema)
    {
        // upgrade user passwords
        $batch_size = 20;
        $i = 0;

        $encoder = $this->container->get('security.password_encoder');

        $em = $this->container->get('doctrine')->getEntityManager();
        $repo = $em->getRepository('CoreBundle:User');
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


    }


}
