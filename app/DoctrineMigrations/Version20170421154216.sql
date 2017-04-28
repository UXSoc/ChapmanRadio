-- phpMyAdmin SQL Dump
-- version 4.2.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 20, 2017 at 10:29 PM
-- Server version: 5.5.35
-- PHP Version: 5.4.37

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `cr_dp01_main`
--

-- --------------------------------------------------------

--
-- Table structure for table `aliases`
--

CREATE TABLE IF NOT EXISTS `aliases` (
  `path` varchar(30) NOT NULL,
  `url` varchar(500) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `expires` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `alterations`
--

CREATE TABLE IF NOT EXISTS `alterations` (
`alterationid` bigint(20) unsigned NOT NULL,
  `starttimestamp` bigint(20) unsigned NOT NULL,
  `endtimestamp` bigint(20) unsigned NOT NULL,
  `showid` bigint(20) unsigned NOT NULL,
  `alteredby` bigint(20) unsigned NOT NULL,
  `note` varchar(600) NOT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4249 ;

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE IF NOT EXISTS `announcements` (
`announcementid` bigint(20) unsigned NOT NULL,
  `twitterid` varchar(400) NOT NULL,
  `announcedon` datetime NOT NULL,
  `message` varchar(600) NOT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3577 ;

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE IF NOT EXISTS `attendance` (
`attendanceid` bigint(20) unsigned NOT NULL,
  `timestamp` bigint(20) unsigned NOT NULL,
  `showid` bigint(20) unsigned NOT NULL,
  `userid` bigint(20) unsigned NOT NULL,
  `status` enum('present','absent','excused','optional','cancelled','absent_again') NOT NULL,
  `late` tinyint(4) NOT NULL,
  `type` enum('show','workshop','event') NOT NULL DEFAULT 'show',
  `season` varchar(10) NOT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=46827 ;

-- --------------------------------------------------------

--
-- Table structure for table `attendance_events`
--

CREATE TABLE IF NOT EXISTS `attendance_events` (
  `timestamp` bigint(20) NOT NULL,
  `eventname` varchar(400) NOT NULL,
  `season` char(6) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `awards`
--

CREATE TABLE IF NOT EXISTS `awards` (
`awardid` bigint(20) unsigned NOT NULL,
  `type` varchar(75) NOT NULL DEFAULT 'showoftheweek',
  `showid` bigint(20) unsigned NOT NULL,
  `awardedon` date NOT NULL,
  `season` varchar(12) NOT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=171 ;

-- --------------------------------------------------------

--
-- Table structure for table `blacklist`
--

CREATE TABLE IF NOT EXISTS `blacklist` (
  `email` varchar(300) NOT NULL,
  `status` enum('blocked','unblocked') NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `calendar`
--

CREATE TABLE IF NOT EXISTS `calendar` (
`calendar_id` int(11) NOT NULL,
  `calendar_datetime` datetime NOT NULL,
  `calendar_text` varchar(255) CHARACTER SET latin1 NOT NULL,
  `calendar_type` enum('public','class','class_new','everyone','other') NOT NULL DEFAULT 'public'
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=95 ;

-- --------------------------------------------------------

--
-- Table structure for table `downtime`
--

CREATE TABLE IF NOT EXISTS `downtime` (
  `datetime` datetime NOT NULL,
  `icecastisdown` tinyint(1) NOT NULL DEFAULT '0',
  `chapmanradioisdown` tinyint(1) NOT NULL DEFAULT '0',
  `chapmanradiolowqualityisdown` tinyint(1) NOT NULL DEFAULT '0',
  `notified` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `emaillists`
--

CREATE TABLE IF NOT EXISTS `emaillists` (
`emaillistid` bigint(20) unsigned NOT NULL,
  `listname` varchar(100) NOT NULL,
  `email` varchar(600) NOT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=19 ;

-- --------------------------------------------------------

--
-- Table structure for table `errors`
--

CREATE TABLE IF NOT EXISTS `errors` (
`errorid` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip` varchar(32) NOT NULL,
  `code` varchar(10) NOT NULL,
  `data` longtext NOT NULL,
  `referer` varchar(300) NOT NULL,
  `useragent` varchar(300) NOT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=399126 ;

-- --------------------------------------------------------

--
-- Table structure for table `evals`
--

CREATE TABLE IF NOT EXISTS `evals` (
`evalid` bigint(20) unsigned NOT NULL,
  `userid` bigint(20) unsigned NOT NULL,
  `showid` bigint(20) unsigned NOT NULL,
  `timestamp` bigint(20) unsigned NOT NULL,
  `postedtimestamp` bigint(20) unsigned NOT NULL,
  `live` tinyint(1) NOT NULL DEFAULT '1',
  `goodbad` enum('good','bad') NOT NULL,
  `type` enum('button','comment') NOT NULL,
  `value` varchar(400) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `season` varchar(10) NOT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8152 ;

-- --------------------------------------------------------

--
-- Table structure for table `eventpics`
--

CREATE TABLE IF NOT EXISTS `eventpics` (
`eventpicid` bigint(20) unsigned NOT NULL,
  `eventid` bigint(20) unsigned NOT NULL,
  `icon` varchar(600) NOT NULL,
  `pic` varchar(600) NOT NULL,
  `full` varchar(600) NOT NULL,
  `caption` varchar(2000) NOT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=59 ;

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE IF NOT EXISTS `events` (
`eventid` bigint(20) unsigned NOT NULL,
  `title` varchar(400) NOT NULL,
  `timestamp` bigint(20) unsigned NOT NULL,
  `description` varchar(2000) NOT NULL,
  `location` varchar(600) NOT NULL,
  `link` varchar(600) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '0',
  `primaryeventpicid` bigint(20) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=34 ;

-- --------------------------------------------------------

--
-- Table structure for table `features`
--

CREATE TABLE IF NOT EXISTS `features` (
`feature_id` bigint(20) NOT NULL,
  `feature_type` enum('normal','html','showoftheweek','recenttracks','upcomingshows') NOT NULL,
  `feature_title` varchar(400) DEFAULT NULL,
  `feature_link` varchar(400) DEFAULT NULL,
  `feature_text` longtext NOT NULL,
  `feature_priority` bigint(20) NOT NULL DEFAULT '0',
  `feature_active` tinyint(1) NOT NULL DEFAULT '0',
  `feature_size` int(11) NOT NULL,
  `feature_posted` datetime NOT NULL,
  `feature_expires` datetime NOT NULL,
  `revisionkey` varchar(30) NOT NULL DEFAULT ''
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=99 ;

-- --------------------------------------------------------

--
-- Table structure for table `feed`
--

CREATE TABLE IF NOT EXISTS `feed` (
  `guid` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` longtext NOT NULL,
  `location` varchar(200) NOT NULL,
  `link` varchar(100) NOT NULL,
  `timestamp` bigint(30) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `finalexam`
--

CREATE TABLE IF NOT EXISTS `finalexam` (
`exam_id` bigint(20) unsigned NOT NULL,
  `exam_user` bigint(20) unsigned NOT NULL,
  `exam_mp3` bigint(20) unsigned NOT NULL,
  `exam_season` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `exam_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=50 ;

-- --------------------------------------------------------

--
-- Table structure for table `genrecontent`
--

CREATE TABLE IF NOT EXISTS `genrecontent` (
  `genre` varchar(40) NOT NULL,
  `content` varchar(6000) NOT NULL,
  `staffid` bigint(20) unsigned NOT NULL,
  `lastmodified` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `genres`
--

CREATE TABLE IF NOT EXISTS `genres` (
  `hour` tinyint(3) unsigned NOT NULL,
  `mon` varchar(57) NOT NULL,
  `tue` varchar(57) NOT NULL,
  `wed` varchar(57) NOT NULL,
  `thu` varchar(57) NOT NULL,
  `fri` varchar(57) NOT NULL,
  `sat` varchar(57) NOT NULL,
  `sun` varchar(57) NOT NULL,
  `season` varchar(10) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `geoip`
--

CREATE TABLE IF NOT EXISTS `geoip` (
  `geoip_ip` varbinary(16) NOT NULL,
  `geoip_countrycode` char(2) CHARACTER SET latin1 NOT NULL,
  `geoip_country` varchar(255) CHARACTER SET latin1 NOT NULL,
  `geoip_region` varchar(255) CHARACTER SET latin1 NOT NULL,
  `geoip_city` varchar(255) CHARACTER SET latin1 NOT NULL,
  `geoip_zip` varchar(255) CHARACTER SET latin1 NOT NULL,
  `geoip_latitude` float NOT NULL,
  `geoip_longitude` float NOT NULL,
  `geoip_timezone` varchar(255) CHARACTER SET latin1 NOT NULL,
  `geoip_lastupdate` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `geoip_old`
--

CREATE TABLE IF NOT EXISTS `geoip_old` (
`geoipid` bigint(20) unsigned NOT NULL,
  `ip1` int(3) unsigned NOT NULL,
  `ip2` int(3) unsigned NOT NULL,
  `ip3` int(3) unsigned NOT NULL,
  `ip4` int(3) unsigned NOT NULL,
  `countrycode` varchar(10) NOT NULL,
  `country` varchar(200) NOT NULL,
  `region` varchar(200) NOT NULL,
  `city` varchar(200) NOT NULL,
  `zip` varchar(10) NOT NULL,
  `lat` float(10,6) NOT NULL,
  `lng` float(10,6) NOT NULL,
  `timezone` varchar(14) NOT NULL,
  `lastsync` bigint(20) NOT NULL,
  `total` mediumint(9) NOT NULL DEFAULT '0'
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=30382 ;

-- --------------------------------------------------------

--
-- Table structure for table `giveaways`
--

CREATE TABLE IF NOT EXISTS `giveaways` (
`giveawayid` bigint(20) unsigned NOT NULL,
  `title` varchar(600) NOT NULL,
  `about` longtext NOT NULL,
  `link` varchar(600) NOT NULL,
  `howtowin` varchar(1000) NOT NULL,
  `hometext` varbinary(600) NOT NULL,
  `shows` varchar(600) NOT NULL,
  `expireson` date NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '0',
  `revisionkey` varchar(30) NOT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=115 ;

-- --------------------------------------------------------

--
-- Table structure for table `giveawayshows`
--

CREATE TABLE IF NOT EXISTS `giveawayshows` (
`id` mediumint(8) unsigned NOT NULL,
  `giveawayid` mediumint(8) unsigned NOT NULL,
  `showid` mediumint(8) unsigned NOT NULL,
  `timestamp` int(10) unsigned NOT NULL,
  `winner` varchar(150) NOT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=45 ;

-- --------------------------------------------------------

--
-- Table structure for table `grade_structure`
--

CREATE TABLE IF NOT EXISTS `grade_structure` (
`grade_id` bigint(20) NOT NULL,
  `grade_name` varchar(255) CHARACTER SET latin1 NOT NULL,
  `grade_type` enum('unknown','category','manual','strikes','evals') CHARACTER SET latin1 NOT NULL,
  `grade_parent` int(11) DEFAULT NULL,
  `grade_season` varchar(10) CHARACTER SET latin1 NOT NULL,
  `grade_condition` enum('equal_to','less_than','less_than_equal','greater_than','greater_than_equal','child_sum') CHARACTER SET latin1 NOT NULL,
  `grade_max` int(11) NOT NULL,
  `grade_target` int(11) DEFAULT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=73 ;

-- --------------------------------------------------------

--
-- Table structure for table `grade_values`
--

CREATE TABLE IF NOT EXISTS `grade_values` (
  `user_id` bigint(20) NOT NULL,
  `grade_id` bigint(20) NOT NULL,
  `grade_value` float NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `listens`
--

CREATE TABLE IF NOT EXISTS `listens` (
`listen_id` bigint(20) unsigned NOT NULL,
  `recording_id` int(11) NOT NULL,
  `timestamp` datetime NOT NULL,
  `source` enum('unknown','download','stream','podcast','live') NOT NULL DEFAULT 'unknown',
  `ipaddr` varbinary(16) NOT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=499461 ;

-- --------------------------------------------------------

--
-- Table structure for table `livechat`
--

CREATE TABLE IF NOT EXISTS `livechat` (
`livechatid` bigint(20) NOT NULL,
  `contactid` varchar(60) NOT NULL,
  `direction` enum('in','out') NOT NULL,
  `message` varchar(1000) NOT NULL,
  `datetime` datetime NOT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=122732 ;

-- --------------------------------------------------------

--
-- Table structure for table `livechat_contacts`
--

CREATE TABLE IF NOT EXISTS `livechat_contacts` (
  `contactkey` varchar(20) NOT NULL,
  `contactname` varchar(200) NOT NULL,
  `contactip` varchar(255) NOT NULL,
  `contactupdated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `locations`
--

CREATE TABLE IF NOT EXISTS `locations` (
`location_id` bigint(20) unsigned NOT NULL,
  `location_zip` varchar(255) NOT NULL,
  `location_city` varchar(255) NOT NULL,
  `location_state` varchar(255) NOT NULL,
  `location_country` varchar(255) NOT NULL,
  `location_latitude` float NOT NULL,
  `location_longitude` float NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=42524 ;

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE IF NOT EXISTS `messages` (
`id` bigint(20) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `name` varchar(200) NOT NULL,
  `email` varchar(200) NOT NULL,
  `subject` varchar(200) NOT NULL,
  `message` longtext NOT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=919 ;

-- --------------------------------------------------------

--
-- Table structure for table `mp3s`
--

CREATE TABLE IF NOT EXISTS `mp3s` (
`mp3id` bigint(20) unsigned NOT NULL,
  `url` varchar(150) NOT NULL,
  `showid` bigint(20) NOT NULL,
  `shortname` varchar(75) NOT NULL,
  `recordedon` datetime NOT NULL,
  `downloads` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `streams` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `podcasts` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `label` varchar(100) NOT NULL,
  `moreinfo` varchar(100) NOT NULL,
  `description` varchar(750) NOT NULL,
  `season` varchar(10) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `clean` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=16746 ;

-- --------------------------------------------------------

--
-- Table structure for table `news`
--

CREATE TABLE IF NOT EXISTS `news` (
`news_id` bigint(20) NOT NULL,
  `news_title` varchar(255) CHARACTER SET latin1 NOT NULL,
  `news_body` longtext CHARACTER SET latin1 NOT NULL,
  `news_postedby` bigint(20) NOT NULL,
  `news_posted` datetime DEFAULT NULL,
  `news_expires` datetime DEFAULT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=21 ;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE IF NOT EXISTS `notifications` (
`notificationid` bigint(20) unsigned NOT NULL,
  `timestamp` bigint(20) unsigned NOT NULL,
  `to` varchar(400) NOT NULL,
  `subject` varchar(400) NOT NULL,
  `body` longtext NOT NULL,
  `headers` varchar(600) NOT NULL,
  `success` tinyint(1) NOT NULL,
  `note` longtext NOT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=133602 ;

-- --------------------------------------------------------

--
-- Table structure for table `nowplaying`
--

CREATE TABLE IF NOT EXISTS `nowplaying` (
`nowplayingid` bigint(20) NOT NULL,
  `timestamp` bigint(20) NOT NULL,
  `showid` bigint(20) NOT NULL,
  `trackid` varchar(36) NOT NULL,
  `text` varchar(600) NOT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=83648 ;

-- --------------------------------------------------------

--
-- Table structure for table `prefs`
--

CREATE TABLE IF NOT EXISTS `prefs` (
  `key` varchar(100) NOT NULL,
  `val` longtext NOT NULL,
  `updated` bigint(20) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `promos`
--

CREATE TABLE IF NOT EXISTS `promos` (
`promoid` bigint(20) unsigned NOT NULL,
  `category` varchar(200) NOT NULL,
  `title` varchar(140) NOT NULL,
  `description` varchar(2000) NOT NULL,
  `expireson` datetime NOT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=193 ;

-- --------------------------------------------------------

--
-- Table structure for table `quizes`
--

CREATE TABLE IF NOT EXISTS `quizes` (
`quizid` bigint(20) unsigned NOT NULL,
  `userid` bigint(20) unsigned NOT NULL,
  `startedon` bigint(20) unsigned NOT NULL,
  `q1` varchar(600) NOT NULL,
  `q2` varchar(600) NOT NULL,
  `q3` varchar(600) NOT NULL,
  `q4` varchar(600) NOT NULL,
  `q5` varchar(600) NOT NULL,
  `q6` varchar(600) NOT NULL,
  `q7` varchar(600) NOT NULL,
  `q8` varchar(600) NOT NULL,
  `q9` varchar(600) NOT NULL,
  `q10` varchar(600) NOT NULL,
  `completed` tinyint(1) NOT NULL DEFAULT '0',
  `right` tinyint(4) NOT NULL,
  `wrong` tinyint(4) NOT NULL,
  `total` tinyint(4) NOT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3030 ;

-- --------------------------------------------------------

--
-- Table structure for table `quizquestions`
--

CREATE TABLE IF NOT EXISTS `quizquestions` (
`quizquestionid` bigint(20) unsigned NOT NULL,
  `question` varchar(600) NOT NULL,
  `responses` varchar(4000) NOT NULL,
  `full` varchar(400) NOT NULL,
  `pic` varchar(400) NOT NULL,
  `icon` varchar(400) NOT NULL,
  `createdby` bigint(20) unsigned NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=29 ;

-- --------------------------------------------------------

--
-- Table structure for table `schedule`
--

CREATE TABLE IF NOT EXISTS `schedule` (
  `hour` tinyint(3) unsigned NOT NULL,
  `mon` varchar(57) NOT NULL DEFAULT ',',
  `tue` varchar(57) NOT NULL DEFAULT ',',
  `wed` varchar(57) NOT NULL DEFAULT ',',
  `thu` varchar(57) NOT NULL DEFAULT ',',
  `fri` varchar(57) NOT NULL DEFAULT ',',
  `sat` varchar(57) NOT NULL DEFAULT ',',
  `sun` varchar(57) NOT NULL DEFAULT ',',
  `season` varchar(10) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `schedule_temp`
--

CREATE TABLE IF NOT EXISTS `schedule_temp` (
`schedule_id` bigint(20) unsigned NOT NULL,
  `schedule_season` char(6) CHARACTER SET latin1 NOT NULL,
  `schedule_day` enum('mon','tue','wed','thu','fri','sat','sun') CHARACTER SET latin1 NOT NULL,
  `schedule_hour` tinyint(4) NOT NULL,
  `schedule_weekoffset` tinyint(4) NOT NULL,
  `schedule_show_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `shows`
--

CREATE TABLE IF NOT EXISTS `shows` (
`showid` bigint(20) unsigned NOT NULL,
  `showname` varchar(100) NOT NULL,
  `showtime` varchar(255) NOT NULL,
  `userid1` bigint(20) unsigned NOT NULL,
  `userid2` bigint(20) unsigned NOT NULL,
  `userid3` bigint(20) unsigned NOT NULL,
  `userid4` bigint(20) unsigned NOT NULL,
  `userid5` bigint(20) unsigned NOT NULL,
  `seasons` varchar(200) NOT NULL,
  `genre` varchar(100) NOT NULL,
  `description` varchar(2000) NOT NULL,
  `musictalk` enum('','music','talk','both') NOT NULL,
  `timestamp2` bigint(20) unsigned NOT NULL,
  `createdon` date NOT NULL,
  `explicit` tinyint(1) NOT NULL DEFAULT '0',
  `turntables` enum('','yes','no','teachme') NOT NULL,
  `podcastcategory` varchar(300) NOT NULL,
  `link` varchar(200) NOT NULL,
  `elevation` smallint(5) NOT NULL DEFAULT '0',
  `swing` tinyint(4) NOT NULL DEFAULT '0',
  `ranking` tinyint(3) unsigned NOT NULL,
  `podcastenabled` tinyint(1) NOT NULL DEFAULT '0',
  `clean` tinyint(1) NOT NULL DEFAULT '0',
  `status` enum('incomplete','finalized','accepted','suspended','cancelled') NOT NULL,
  `attendanceoptional` tinyint(1) NOT NULL DEFAULT '0',
  `app_differentiate` varchar(1200) NOT NULL,
  `app_promote` varchar(1200) NOT NULL,
  `app_timeline` varchar(1200) NOT NULL,
  `app_giveaway` varchar(1200) NOT NULL,
  `app_speaking` varchar(1200) NOT NULL,
  `app_equipment` varchar(1200) NOT NULL,
  `app_prepare` varchar(1200) NOT NULL,
  `app_examples` varchar(1200) NOT NULL,
  `availability` varchar(1000) NOT NULL,
  `availabilitynotes` varchar(1200) NOT NULL,
  `revisionkey` varchar(30) DEFAULT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1310 ;

-- --------------------------------------------------------

--
-- Table structure for table `show_aliases`
--

CREATE TABLE IF NOT EXISTS `show_aliases` (
  `from_show_id` int(11) NOT NULL,
  `to_show_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `show_sitins`
--

CREATE TABLE IF NOT EXISTS `show_sitins` (
  `showid` int(11) NOT NULL,
  `season` char(6) NOT NULL,
  `result` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `sports`
--

CREATE TABLE IF NOT EXISTS `sports` (
`id` mediumint(8) unsigned NOT NULL,
  `datetime` datetime NOT NULL,
  `onair` tinyint(1) NOT NULL,
  `sport` varchar(255) NOT NULL,
  `gamename` varchar(255) NOT NULL,
  `ourname` varchar(255) NOT NULL,
  `theirname` varchar(255) NOT NULL,
  `ourscore` smallint(6) NOT NULL,
  `theirscore` smallint(6) NOT NULL,
  `notes` mediumtext NOT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=81 ;

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE IF NOT EXISTS `staff` (
`staffid` bigint(20) NOT NULL,
  `userid` bigint(20) unsigned NOT NULL,
  `season` varchar(10) NOT NULL,
  `title` varchar(120) NOT NULL,
  `perms` varchar(100) NOT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=44 ;

-- --------------------------------------------------------

--
-- Table structure for table `staff_log`
--

CREATE TABLE IF NOT EXISTS `staff_log` (
`logid` bigint(20) NOT NULL,
  `userid` bigint(20) NOT NULL,
  `timestamp` datetime NOT NULL,
  `details` longtext NOT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8320 ;

-- --------------------------------------------------------

--
-- Table structure for table `stats`
--

CREATE TABLE IF NOT EXISTS `stats` (
  `datetime` datetime NOT NULL,
  `showid` bigint(20) NOT NULL DEFAULT '0',
  `chapmanradio` smallint(6) unsigned NOT NULL DEFAULT '0',
  `chapmanradiolowquality` smallint(6) unsigned NOT NULL DEFAULT '0',
  `sports` smallint(6) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `strikes`
--

CREATE TABLE IF NOT EXISTS `strikes` (
`strikeid` bigint(20) unsigned NOT NULL,
  `userid` bigint(20) unsigned NOT NULL,
  `assignedon` datetime NOT NULL,
  `reason` enum('show_absence','show_tardies','workshop_absence','peer_evals','inappropriate_conduct') NOT NULL,
  `emailsent` tinyint(1) NOT NULL DEFAULT '0',
  `season` varchar(10) NOT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5776 ;

-- --------------------------------------------------------

--
-- Table structure for table `suspendedloginattempts`
--

CREATE TABLE IF NOT EXISTS `suspendedloginattempts` (
`attemptid` bigint(20) unsigned NOT NULL,
  `userid` bigint(20) unsigned NOT NULL,
  `timestamp` bigint(20) unsigned NOT NULL,
  `type` enum('account_suspended','show_cancelled') NOT NULL,
  `season` varchar(10) NOT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=769 ;

-- --------------------------------------------------------

--
-- Table structure for table `tags`
--

CREATE TABLE IF NOT EXISTS `tags` (
`tagid` bigint(20) unsigned NOT NULL,
  `showid` bigint(20) unsigned NOT NULL,
  `label` varchar(200) NOT NULL,
  `url` varchar(200) NOT NULL,
  `uploadedon` datetime NOT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=17 ;

-- --------------------------------------------------------

--
-- Table structure for table `tracks`
--

CREATE TABLE IF NOT EXISTS `tracks` (
  `track_id` int(11) NOT NULL,
  `artist_id` int(11) NOT NULL,
  `track_name` varchar(200) NOT NULL,
  `artist_name` varchar(200) NOT NULL,
  `img_base` varchar(500) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `training_signups`
--

CREATE TABLE IF NOT EXISTS `training_signups` (
`trainingsignup_id` bigint(20) unsigned NOT NULL,
  `trainingsignup_slot` bigint(20) unsigned NOT NULL,
  `trainingsignup_userid` bigint(20) unsigned NOT NULL,
  `trainingsignup_present` enum('0','1') NOT NULL DEFAULT '0'
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=495 ;

-- --------------------------------------------------------

--
-- Table structure for table `training_slots`
--

CREATE TABLE IF NOT EXISTS `training_slots` (
`trainingslot_id` bigint(20) unsigned NOT NULL,
  `trainingslot_season` char(6) NOT NULL,
  `trainingslot_datetime` datetime NOT NULL,
  `trainingslot_staffid` bigint(20) unsigned NOT NULL,
  `trainingslot_max` tinyint(4) NOT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=280 ;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
`userid` bigint(20) unsigned NOT NULL,
  `fbid` bigint(20) unsigned NOT NULL,
  `email` varchar(200) NOT NULL,
  `studentid` bigint(20) NOT NULL,
  `phone` varchar(30) NOT NULL,
  `fname` varchar(100) NOT NULL,
  `lname` varchar(100) NOT NULL,
  `name` varchar(120) NOT NULL,
  `djname` varchar(120) NOT NULL,
  `gender` varchar(100) NOT NULL,
  `seasons` varchar(140) NOT NULL,
  `classclub` enum('class','club') NOT NULL,
  `petpreference` varchar(255) NOT NULL DEFAULT 'none',
  `lastlogin` datetime NOT NULL,
  `lastip` varchar(30) NOT NULL,
  `password` varchar(48) NOT NULL,
  `verifycode` varchar(30) NOT NULL,
  `type` enum('','dj','staff') NOT NULL,
  `staffgroup` varchar(200) NOT NULL,
  `staffposition` varchar(200) NOT NULL,
  `staffemail` varchar(200) NOT NULL,
  `confirmnewsletter` tinyint(1) NOT NULL DEFAULT '0',
  `workshoprequired` tinyint(1) NOT NULL DEFAULT '1',
  `suspended` tinyint(1) NOT NULL DEFAULT '0',
  `quizpassedseasons` varchar(600) NOT NULL,
  `revisionkey` varchar(30) NOT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1825 ;

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_features_active`
--
CREATE TABLE IF NOT EXISTS `v_features_active` (
`feature_id` bigint(20)
,`feature_type` enum('normal','html','showoftheweek','recenttracks','upcomingshows')
,`feature_title` varchar(400)
,`feature_link` varchar(400)
,`feature_text` longtext
,`feature_priority` bigint(20)
,`feature_active` tinyint(1)
,`feature_size` int(11)
,`feature_posted` datetime
,`feature_expires` datetime
,`revisionkey` varchar(30)
);
-- --------------------------------------------------------

--
-- Stand-in structure for view `v_listener_map`
--
CREATE TABLE IF NOT EXISTS `v_listener_map` (
`listen_timestamp` datetime
,`listen_source` enum('unknown','download','stream','podcast','live')
,`geoip_country` varchar(255)
,`geoip_region` varchar(255)
,`geoip_city` varchar(255)
,`geoip_latitude` double
,`geoip_longitude` double
,`listen_count` bigint(21)
);
-- --------------------------------------------------------

--
-- Stand-in structure for view `v_livechat`
--
CREATE TABLE IF NOT EXISTS `v_livechat` (
`livechatid` bigint(20)
,`contactid` varchar(60)
,`direction` enum('in','out')
,`message` varchar(1000)
,`datetime` datetime
,`contactkey` varchar(20)
,`contactname` varchar(200)
);
-- --------------------------------------------------------

--
-- Stand-in structure for view `v_news_active`
--
CREATE TABLE IF NOT EXISTS `v_news_active` (
`news_id` bigint(20)
,`news_title` varchar(255)
,`news_body` longtext
,`news_postedby` bigint(20)
,`news_posted` datetime
,`news_expires` datetime
);
-- --------------------------------------------------------

--
-- Stand-in structure for view `v_training_signups`
--
CREATE TABLE IF NOT EXISTS `v_training_signups` (
`trainingsignup_id` bigint(20) unsigned
,`trainingsignup_slot` bigint(20) unsigned
,`trainingsignup_userid` bigint(20) unsigned
,`trainingsignup_present` enum('0','1')
,`trainingslot_id` bigint(20) unsigned
,`trainingslot_season` char(6)
,`trainingslot_datetime` datetime
,`trainingslot_staffid` bigint(20) unsigned
,`trainingslot_max` tinyint(4)
);
-- --------------------------------------------------------

--
-- Stand-in structure for view `v_training_slots`
--
CREATE TABLE IF NOT EXISTS `v_training_slots` (
`trainingslot_id` bigint(20) unsigned
,`trainingslot_season` char(6)
,`trainingslot_datetime` datetime
,`trainingslot_staffid` bigint(20) unsigned
,`trainingslot_max` tinyint(4)
,`userid` bigint(20) unsigned
,`fbid` bigint(20) unsigned
,`email` varchar(200)
,`studentid` bigint(20)
,`phone` varchar(30)
,`fname` varchar(100)
,`lname` varchar(100)
,`name` varchar(120)
,`djname` varchar(120)
,`gender` varchar(100)
,`seasons` varchar(140)
,`classclub` enum('class','club')
,`lastlogin` datetime
,`lastip` varchar(30)
,`password` varchar(48)
,`verifycode` varchar(30)
,`type` enum('','dj','staff')
,`staffgroup` varchar(200)
,`staffposition` varchar(200)
,`staffemail` varchar(200)
,`confirmnewsletter` tinyint(1)
,`workshoprequired` tinyint(1)
,`suspended` tinyint(1)
,`quizpassedseasons` varchar(600)
,`revisionkey` varchar(30)
,`trainingslot_count` bigint(21)
);
-- --------------------------------------------------------

--
-- Structure for view `v_features_active`
--
DROP TABLE IF EXISTS `v_features_active`;

CREATE VIEW `v_features_active` AS select `features`.`feature_id` AS `feature_id`,`features`.`feature_type` AS `feature_type`,`features`.`feature_title` AS `feature_title`,`features`.`feature_link` AS `feature_link`,`features`.`feature_text` AS `feature_text`,`features`.`feature_priority` AS `feature_priority`,`features`.`feature_active` AS `feature_active`,`features`.`feature_size` AS `feature_size`,`features`.`feature_posted` AS `feature_posted`,`features`.`feature_expires` AS `feature_expires`,`features`.`revisionkey` AS `revisionkey` from `features` where ((`features`.`feature_active` = 1) and (isnull(`features`.`feature_expires`) or (`features`.`feature_expires` = '0000-00-00 00:00:00') or (`features`.`feature_expires` > now())) and (isnull(`features`.`feature_posted`) or (`features`.`feature_posted` < now()))) order by `features`.`feature_priority` desc;

-- --------------------------------------------------------

--
-- Structure for view `v_listener_map`
--
DROP TABLE IF EXISTS `v_listener_map`;

CREATE VIEW `v_listener_map` AS select max(`listens`.`timestamp`) AS `listen_timestamp`,max(`listens`.`source`) AS `listen_source`,`geoip`.`geoip_country` AS `geoip_country`,`geoip`.`geoip_region` AS `geoip_region`,`geoip`.`geoip_city` AS `geoip_city`,avg(`geoip`.`geoip_latitude`) AS `geoip_latitude`,avg(`geoip`.`geoip_longitude`) AS `geoip_longitude`,count(0) AS `listen_count` from (`listens` join `geoip` on((`geoip`.`geoip_ip` = `listens`.`ipaddr`))) where (`geoip`.`geoip_city` <> '') group by `geoip`.`geoip_country`,`geoip`.`geoip_region`,`geoip`.`geoip_city`;

-- --------------------------------------------------------

--
-- Structure for view `v_livechat`
--
DROP TABLE IF EXISTS `v_livechat`;

CREATE VIEW `v_livechat` AS select `livechat`.`livechatid` AS `livechatid`,`livechat`.`contactid` AS `contactid`,`livechat`.`direction` AS `direction`,`livechat`.`message` AS `message`,`livechat`.`datetime` AS `datetime`,`livechat_contacts`.`contactkey` AS `contactkey`,`livechat_contacts`.`contactname` AS `contactname` from (`livechat` left join `livechat_contacts` on((`livechat`.`contactid` = `livechat_contacts`.`contactkey`)));

-- --------------------------------------------------------

--
-- Structure for view `v_news_active`
--
DROP TABLE IF EXISTS `v_news_active`;

CREATE VIEW `v_news_active` AS select `news`.`news_id` AS `news_id`,`news`.`news_title` AS `news_title`,`news`.`news_body` AS `news_body`,`news`.`news_postedby` AS `news_postedby`,`news`.`news_posted` AS `news_posted`,`news`.`news_expires` AS `news_expires` from `news` where ((isnull(`news`.`news_posted`) or (`news`.`news_posted` < now())) and (isnull(`news`.`news_expires`) or (`news`.`news_expires` > now()))) order by `news`.`news_posted` desc;

-- --------------------------------------------------------

--
-- Structure for view `v_training_signups`
--
DROP TABLE IF EXISTS `v_training_signups`;

CREATE VIEW `v_training_signups` AS select `training_signups`.`trainingsignup_id` AS `trainingsignup_id`,`training_signups`.`trainingsignup_slot` AS `trainingsignup_slot`,`training_signups`.`trainingsignup_userid` AS `trainingsignup_userid`,`training_signups`.`trainingsignup_present` AS `trainingsignup_present`,`training_slots`.`trainingslot_id` AS `trainingslot_id`,`training_slots`.`trainingslot_season` AS `trainingslot_season`,`training_slots`.`trainingslot_datetime` AS `trainingslot_datetime`,`training_slots`.`trainingslot_staffid` AS `trainingslot_staffid`,`training_slots`.`trainingslot_max` AS `trainingslot_max` from (`training_signups` join `training_slots` on((`training_signups`.`trainingsignup_slot` = `training_slots`.`trainingslot_id`)));

-- --------------------------------------------------------

--
-- Structure for view `v_training_slots`
--
DROP TABLE IF EXISTS `v_training_slots`;

CREATE VIEW `v_training_slots` AS select `training_slots`.`trainingslot_id` AS `trainingslot_id`,`training_slots`.`trainingslot_season` AS `trainingslot_season`,`training_slots`.`trainingslot_datetime` AS `trainingslot_datetime`,`training_slots`.`trainingslot_staffid` AS `trainingslot_staffid`,`training_slots`.`trainingslot_max` AS `trainingslot_max`,`users`.`userid` AS `userid`,`users`.`fbid` AS `fbid`,`users`.`email` AS `email`,`users`.`studentid` AS `studentid`,`users`.`phone` AS `phone`,`users`.`fname` AS `fname`,`users`.`lname` AS `lname`,`users`.`name` AS `name`,`users`.`djname` AS `djname`,`users`.`gender` AS `gender`,`users`.`seasons` AS `seasons`,`users`.`classclub` AS `classclub`,`users`.`lastlogin` AS `lastlogin`,`users`.`lastip` AS `lastip`,`users`.`password` AS `password`,`users`.`verifycode` AS `verifycode`,`users`.`type` AS `type`,`users`.`staffgroup` AS `staffgroup`,`users`.`staffposition` AS `staffposition`,`users`.`staffemail` AS `staffemail`,`users`.`confirmnewsletter` AS `confirmnewsletter`,`users`.`workshoprequired` AS `workshoprequired`,`users`.`suspended` AS `suspended`,`users`.`quizpassedseasons` AS `quizpassedseasons`,`users`.`revisionkey` AS `revisionkey`,(select count(0) from `training_signups` where (`training_signups`.`trainingsignup_slot` = `training_slots`.`trainingslot_id`)) AS `trainingslot_count` from (`training_slots` join `users` on((`training_slots`.`trainingslot_staffid` = `users`.`userid`)));

--
-- Indexes for dumped tables
--

--
-- Indexes for table `aliases`
--
ALTER TABLE `aliases`
 ADD PRIMARY KEY (`path`);

--
-- Indexes for table `alterations`
--
ALTER TABLE `alterations`
 ADD PRIMARY KEY (`alterationid`);

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
 ADD PRIMARY KEY (`announcementid`);

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
 ADD PRIMARY KEY (`attendanceid`);

--
-- Indexes for table `attendance_events`
--
ALTER TABLE `attendance_events`
 ADD PRIMARY KEY (`timestamp`);

--
-- Indexes for table `awards`
--
ALTER TABLE `awards`
 ADD PRIMARY KEY (`awardid`);

--
-- Indexes for table `blacklist`
--
ALTER TABLE `blacklist`
 ADD PRIMARY KEY (`email`);

--
-- Indexes for table `calendar`
--
ALTER TABLE `calendar`
 ADD PRIMARY KEY (`calendar_id`);

--
-- Indexes for table `downtime`
--
ALTER TABLE `downtime`
 ADD PRIMARY KEY (`datetime`);

--
-- Indexes for table `emaillists`
--
ALTER TABLE `emaillists`
 ADD PRIMARY KEY (`emaillistid`);

--
-- Indexes for table `errors`
--
ALTER TABLE `errors`
 ADD PRIMARY KEY (`errorid`);

--
-- Indexes for table `evals`
--
ALTER TABLE `evals`
 ADD PRIMARY KEY (`evalid`);

--
-- Indexes for table `eventpics`
--
ALTER TABLE `eventpics`
 ADD PRIMARY KEY (`eventpicid`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
 ADD PRIMARY KEY (`eventid`);

--
-- Indexes for table `features`
--
ALTER TABLE `features`
 ADD PRIMARY KEY (`feature_id`);

--
-- Indexes for table `feed`
--
ALTER TABLE `feed`
 ADD PRIMARY KEY (`guid`);

--
-- Indexes for table `finalexam`
--
ALTER TABLE `finalexam`
 ADD PRIMARY KEY (`exam_id`);

--
-- Indexes for table `genrecontent`
--
ALTER TABLE `genrecontent`
 ADD PRIMARY KEY (`genre`);

--
-- Indexes for table `geoip`
--
ALTER TABLE `geoip`
 ADD PRIMARY KEY (`geoip_ip`);

--
-- Indexes for table `geoip_old`
--
ALTER TABLE `geoip_old`
 ADD PRIMARY KEY (`geoipid`);

--
-- Indexes for table `giveaways`
--
ALTER TABLE `giveaways`
 ADD PRIMARY KEY (`giveawayid`);

--
-- Indexes for table `giveawayshows`
--
ALTER TABLE `giveawayshows`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `grade_structure`
--
ALTER TABLE `grade_structure`
 ADD PRIMARY KEY (`grade_id`);

--
-- Indexes for table `grade_values`
--
ALTER TABLE `grade_values`
 ADD PRIMARY KEY (`user_id`,`grade_id`);

--
-- Indexes for table `listens`
--
ALTER TABLE `listens`
 ADD PRIMARY KEY (`listen_id`);

--
-- Indexes for table `livechat`
--
ALTER TABLE `livechat`
 ADD PRIMARY KEY (`livechatid`);

--
-- Indexes for table `livechat_contacts`
--
ALTER TABLE `livechat_contacts`
 ADD PRIMARY KEY (`contactkey`);

--
-- Indexes for table `locations`
--
ALTER TABLE `locations`
 ADD PRIMARY KEY (`location_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mp3s`
--
ALTER TABLE `mp3s`
 ADD PRIMARY KEY (`mp3id`);

--
-- Indexes for table `news`
--
ALTER TABLE `news`
 ADD PRIMARY KEY (`news_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
 ADD PRIMARY KEY (`notificationid`);

--
-- Indexes for table `nowplaying`
--
ALTER TABLE `nowplaying`
 ADD PRIMARY KEY (`nowplayingid`);

--
-- Indexes for table `prefs`
--
ALTER TABLE `prefs`
 ADD PRIMARY KEY (`key`);

--
-- Indexes for table `promos`
--
ALTER TABLE `promos`
 ADD PRIMARY KEY (`promoid`);

--
-- Indexes for table `quizes`
--
ALTER TABLE `quizes`
 ADD PRIMARY KEY (`quizid`);

--
-- Indexes for table `quizquestions`
--
ALTER TABLE `quizquestions`
 ADD PRIMARY KEY (`quizquestionid`);

--
-- Indexes for table `schedule`
--
ALTER TABLE `schedule`
 ADD PRIMARY KEY (`hour`,`season`);

--
-- Indexes for table `schedule_temp`
--
ALTER TABLE `schedule_temp`
 ADD PRIMARY KEY (`schedule_id`);

--
-- Indexes for table `shows`
--
ALTER TABLE `shows`
 ADD PRIMARY KEY (`showid`);

--
-- Indexes for table `show_aliases`
--
ALTER TABLE `show_aliases`
 ADD PRIMARY KEY (`from_show_id`,`to_show_id`);

--
-- Indexes for table `show_sitins`
--
ALTER TABLE `show_sitins`
 ADD PRIMARY KEY (`showid`,`season`);

--
-- Indexes for table `sports`
--
ALTER TABLE `sports`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
 ADD PRIMARY KEY (`staffid`);

--
-- Indexes for table `staff_log`
--
ALTER TABLE `staff_log`
 ADD PRIMARY KEY (`logid`);

--
-- Indexes for table `stats`
--
ALTER TABLE `stats`
 ADD PRIMARY KEY (`datetime`);

--
-- Indexes for table `strikes`
--
ALTER TABLE `strikes`
 ADD PRIMARY KEY (`strikeid`);

--
-- Indexes for table `suspendedloginattempts`
--
ALTER TABLE `suspendedloginattempts`
 ADD PRIMARY KEY (`attemptid`);

--
-- Indexes for table `tags`
--
ALTER TABLE `tags`
 ADD PRIMARY KEY (`tagid`);

--
-- Indexes for table `tracks`
--
ALTER TABLE `tracks`
 ADD PRIMARY KEY (`track_id`), ADD KEY `track_name` (`track_name`), ADD KEY `artist_name` (`artist_name`), ADD KEY `artist_id` (`artist_id`);

--
-- Indexes for table `training_signups`
--
ALTER TABLE `training_signups`
 ADD PRIMARY KEY (`trainingsignup_id`);

--
-- Indexes for table `training_slots`
--
ALTER TABLE `training_slots`
 ADD PRIMARY KEY (`trainingslot_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
 ADD PRIMARY KEY (`userid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `alterations`
--
ALTER TABLE `alterations`
MODIFY `alterationid` bigint(20) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4249;
--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
MODIFY `announcementid` bigint(20) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3577;
--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
MODIFY `attendanceid` bigint(20) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=46827;
--
-- AUTO_INCREMENT for table `awards`
--
ALTER TABLE `awards`
MODIFY `awardid` bigint(20) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=171;
--
-- AUTO_INCREMENT for table `calendar`
--
ALTER TABLE `calendar`
MODIFY `calendar_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=95;
--
-- AUTO_INCREMENT for table `emaillists`
--
ALTER TABLE `emaillists`
MODIFY `emaillistid` bigint(20) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=19;
--
-- AUTO_INCREMENT for table `errors`
--
ALTER TABLE `errors`
MODIFY `errorid` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=399126;
--
-- AUTO_INCREMENT for table `evals`
--
ALTER TABLE `evals`
MODIFY `evalid` bigint(20) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=8152;
--
-- AUTO_INCREMENT for table `eventpics`
--
ALTER TABLE `eventpics`
MODIFY `eventpicid` bigint(20) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=59;
--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
MODIFY `eventid` bigint(20) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=34;
--
-- AUTO_INCREMENT for table `features`
--
ALTER TABLE `features`
MODIFY `feature_id` bigint(20) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=99;
--
-- AUTO_INCREMENT for table `finalexam`
--
ALTER TABLE `finalexam`
MODIFY `exam_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=50;
--
-- AUTO_INCREMENT for table `geoip_old`
--
ALTER TABLE `geoip_old`
MODIFY `geoipid` bigint(20) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=30382;
--
-- AUTO_INCREMENT for table `giveaways`
--
ALTER TABLE `giveaways`
MODIFY `giveawayid` bigint(20) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=115;
--
-- AUTO_INCREMENT for table `giveawayshows`
--
ALTER TABLE `giveawayshows`
MODIFY `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=45;
--
-- AUTO_INCREMENT for table `grade_structure`
--
ALTER TABLE `grade_structure`
MODIFY `grade_id` bigint(20) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=73;
--
-- AUTO_INCREMENT for table `listens`
--
ALTER TABLE `listens`
MODIFY `listen_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=499461;
--
-- AUTO_INCREMENT for table `livechat`
--
ALTER TABLE `livechat`
MODIFY `livechatid` bigint(20) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=122732;
--
-- AUTO_INCREMENT for table `locations`
--
ALTER TABLE `locations`
MODIFY `location_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=42524;
--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=919;
--
-- AUTO_INCREMENT for table `mp3s`
--
ALTER TABLE `mp3s`
MODIFY `mp3id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=16746;
--
-- AUTO_INCREMENT for table `news`
--
ALTER TABLE `news`
MODIFY `news_id` bigint(20) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=21;
--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
MODIFY `notificationid` bigint(20) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=133602;
--
-- AUTO_INCREMENT for table `nowplaying`
--
ALTER TABLE `nowplaying`
MODIFY `nowplayingid` bigint(20) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=83648;
--
-- AUTO_INCREMENT for table `promos`
--
ALTER TABLE `promos`
MODIFY `promoid` bigint(20) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=193;
--
-- AUTO_INCREMENT for table `quizes`
--
ALTER TABLE `quizes`
MODIFY `quizid` bigint(20) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3030;
--
-- AUTO_INCREMENT for table `quizquestions`
--
ALTER TABLE `quizquestions`
MODIFY `quizquestionid` bigint(20) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=29;
--
-- AUTO_INCREMENT for table `schedule_temp`
--
ALTER TABLE `schedule_temp`
MODIFY `schedule_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `shows`
--
ALTER TABLE `shows`
MODIFY `showid` bigint(20) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1310;
--
-- AUTO_INCREMENT for table `sports`
--
ALTER TABLE `sports`
MODIFY `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=81;
--
-- AUTO_INCREMENT for table `staff`
--
ALTER TABLE `staff`
MODIFY `staffid` bigint(20) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=44;
--
-- AUTO_INCREMENT for table `staff_log`
--
ALTER TABLE `staff_log`
MODIFY `logid` bigint(20) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=8320;
--
-- AUTO_INCREMENT for table `strikes`
--
ALTER TABLE `strikes`
MODIFY `strikeid` bigint(20) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5776;
--
-- AUTO_INCREMENT for table `suspendedloginattempts`
--
ALTER TABLE `suspendedloginattempts`
MODIFY `attemptid` bigint(20) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=769;
--
-- AUTO_INCREMENT for table `tags`
--
ALTER TABLE `tags`
MODIFY `tagid` bigint(20) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=17;
--
-- AUTO_INCREMENT for table `training_signups`
--
ALTER TABLE `training_signups`
MODIFY `trainingsignup_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=495;
--
-- AUTO_INCREMENT for table `training_slots`
--
ALTER TABLE `training_slots`
MODIFY `trainingslot_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=280;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
MODIFY `userid` bigint(20) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1825;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
