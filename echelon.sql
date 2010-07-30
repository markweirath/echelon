-- phpMyAdmin SQL Dump
-- version 3.2.0.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jul 23, 2010 at 12:11 AM
-- Server version: 5.1.36
-- PHP Version: 5.3.0

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `echelon`
--

-- --------------------------------------------------------

--
-- Table structure for table `ech_blacklist`
--

CREATE TABLE IF NOT EXISTS `ech_blacklist` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `ip` varchar(24) NOT NULL,
  `active` tinyint(4) DEFAULT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `time_add` int(32) unsigned DEFAULT NULL,
  `admin_id` smallint(6) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ip` (`ip`,`active`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `ech_config`
--

CREATE TABLE IF NOT EXISTS `ech_config` (
  `id` smallint(6) NOT NULL AUTO_INCREMENT,
  `name` varchar(25) NOT NULL,
  `value` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `i_config` (`name`,`value`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=16 ;

--
-- Dumping data for table `ech_config`
--

INSERT INTO `ech_config` (`id`, `name`, `value`) VALUES
(1, 'name', 'Development!'),
(2, 'num_games', '2'),
(3, 'limit_rows', '50'),
(4, 'min_pw_len', '8'),
(5, 'user_key_expire', '14'),
(6, 'email', 'admin@example.com'),
(7, 'admin_name', 'Admin'),
(8, 'https', '0'),
(9, 'allow_ie', '1'),
(10, 'time_format', 'D, d/m/y (H:i)'),
(11, 'time_zone', 'Europe/Dublin'),
(12, 'email_header', 'Hello %name%, This is an email from the Echelon admins.'),
(13, 'email_footer', 'Thanks, the %ech_name% Echelon Team'),
(14, 'pw_req_level', '1'),
(15, 'pw_req_level_group', '64');

-- --------------------------------------------------------

--
-- Table structure for table `ech_games`
--

CREATE TABLE IF NOT EXISTS `ech_games` (
  `id` smallint(8) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `game` varchar(255) NOT NULL,
  `name_short` varchar(255) DEFAULT NULL,
  `num_srvs` smallint(9) NOT NULL,
  `db_host` varchar(255) NOT NULL,
  `db_user` varchar(255) NOT NULL,
  `db_pw` varchar(255) DEFAULT NULL,
  `db_name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `i_games` (`name`,`num_srvs`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `ech_groups`
--

CREATE TABLE IF NOT EXISTS `ech_groups` (
  `id` smallint(4) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  `display` varchar(255) DEFAULT NULL,
  `premissions` varchar(512) NOT NULL,
  PRIMARY KEY (`id`,`name`),
  KEY `i_name` (`name`,`display`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `ech_groups`
--

INSERT INTO `ech_groups` (`id`, `name`, `display`, `premissions`) VALUES
(1, 'visitor', 'Visitor', '1'),
(2, 'siteadmin', 'Site Admin', '1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30'),
(3, 'senioradmin', 'Senior Admin', '1,2,3,4,5,8,12,14,16,17,20,21,22,23,24'),
(4, 'admin', 'Admin', '1,2,3,4,5,8,16,17,20,21,22'),
(5, 'mod', 'Moderator', '1,2,3,4,5,8,16,22');

-- --------------------------------------------------------

--
-- Table structure for table `ech_links`
--

CREATE TABLE IF NOT EXISTS `ech_links` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `url` varchar(255) NOT NULL,
  `name` varchar(80) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `i_url` (`url`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `ech_links`
--

INSERT INTO `ech_links` (`id`, `url`, `name`, `title`) VALUES
(1, 'http://wiki.bigbrotherbot.net/doku.php/echelon', 'Echelon Help Wiki', 'Documentation for the installation and use of Echelon'),
(2, 'http://echelon.bigbrotherbot.net/', 'Echelon Home', 'Home site of Echelon project, check here for development news, updates, and information regarding Echelon'),
(3, 'http://eire32designs.com', 'Eire32 Site', 'The developers site'),
(4, 'http://www.bigbrotherbot.net/forums/', 'B3 Site', 'Home of bigbrother bot');

-- --------------------------------------------------------

--
-- Table structure for table `ech_logs`
--

CREATE TABLE IF NOT EXISTS `ech_logs` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(64) DEFAULT NULL,
  `msg` varchar(255) DEFAULT '',
  `client_id` smallint(5) DEFAULT NULL,
  `user_id` smallint(5) DEFAULT NULL,
  `time_add` int(32) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `i_logs` (`client_id`,`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `ech_permissions`
--

CREATE TABLE IF NOT EXISTS `ech_permissions` (
  `id` mediumint(6) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`,`name`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=27 ;

--
-- Dumping data for table `ech_permissions`
--

INSERT INTO `ech_permissions` (`id`, `name`, `description`) VALUES
(1, 'login', 'Allows the user to login'),
(2, 'clients', 'Allows the user to view the client listing'),
(3, 'chatlogs', 'Allows the user to view Chatlogs'),
(4, 'penalties', 'Allows the user to view the Penalty Listing pages'),
(5, 'admins', 'Allows the user to view the Admins Pages'),
(6, 'manage_settings', 'Allows the user to Manage Echelon Settings.'),
(7, 'pbss', 'Allows the user to view PBSS (If Enabled)'),
(8, 'logs', 'Allows the user to view Logs'),
(9, 'edit_user', 'Allows the user to edit other Echelon users'),
(10, 'add_user', 'Allows the user to Add Echelon Users'),
(11, 'manage_servers', 'Allows the user to Manage Servers'),
(12, 'ban', 'Allows the user to Ban'),
(13, 'edit_mask', 'Allows the user to Edit User Level Masks'),
(14, 'siteadmin', 'Allows the user to  control the site blacklist and other admin actions'),
(15, 'edit_perms', 'Allows the user to the premissions of user groups and users'),
(16, 'comment', 'Allows a user to add a comment to a client'),
(17, 'greeting', 'Allows the user to change the greeting of a client'),
(18, 'edit_client_level', 'Allows user to change a players B3 level'),
(19, 'edit_ban', 'Allows user to edit a B3 ban'),
(20, 'view_ip', 'Allows the user to view players IP addresses'),
(21, 'view_full_guid', 'Allow the user to view players full GUID for clients'),
(22, 'view_half_guid', 'Allow the user to view half of the player GUID for clients'),
(23, 'unban', 'Allows user to remove a B3 Ban'),
(24, 'edit_xlrstats', 'Allows user to edit a client''s XLRStats information (hidden, fixed name)'),
(25, 'ctime', 'Allows user to view CTime information'),
(26, 'see_update_msg', 'Shows this user the Echelon needs updating message');

-- --------------------------------------------------------

--
-- Table structure for table `ech_plugins`
--

CREATE TABLE IF NOT EXISTS `ech_plugins` (
  `id` smallint(8) unsigned NOT NULL AUTO_INCREMENT,
  `game_id` smallint(8) unsigned NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `info` varchar(255) DEFAULT NULL,
  `enabled` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `i_plug` (`game_id`,`enabled`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;


-- --------------------------------------------------------

--
-- Table structure for table `ech_servers`
--

CREATE TABLE IF NOT EXISTS `ech_servers` (
  `id` int(5) unsigned NOT NULL AUTO_INCREMENT,
  `game` smallint(5) NOT NULL,
  `name` varchar(100) NOT NULL,
  `ip` varchar(15) NOT NULL,
  `pb_active` tinyint(1) NOT NULL,
  `rcon_pass` varchar(50) NOT NULL,
  `rcon_ip` varchar(26) NOT NULL,
  `rcon_port` int(5) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `game` (`game`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `ech_users`
--

CREATE TABLE IF NOT EXISTS `ech_users` (
  `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(32) NOT NULL,
  `display` varchar(32) DEFAULT NULL,
  `email` varchar(32) DEFAULT NULL,
  `password` varchar(64) NOT NULL,
  `salt` varchar(12) NOT NULL,
  `ip` varchar(24) DEFAULT NULL,
  `ech_group` smallint(4) unsigned NOT NULL DEFAULT '0',
  `admin_id` smallint(6) unsigned NOT NULL DEFAULT '0',
  `first_seen` int(24) DEFAULT NULL,
  `last_seen` int(24) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `password` (`password`),
  KEY `salt` (`salt`),
  KEY `i_group` (`ech_group`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `ech_user_keys`
--

CREATE TABLE IF NOT EXISTS `ech_user_keys` (
  `reg_key` varchar(40) NOT NULL,
  `ech_group` smallint(4) NOT NULL,
  `admin_id` smallint(5) unsigned NOT NULL,
  `comment` varchar(500) DEFAULT NULL,
  `time_add` mediumint(24) unsigned DEFAULT NULL,
  `email` varchar(160) NOT NULL,
  `active` tinyint(4) NOT NULL,
  PRIMARY KEY (`reg_key`),
  KEY `i_regkey` (`active`,`email`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
