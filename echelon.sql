/*
MySQL Data Transfer
Source Host: localhost
Source Database: echelon
Target Host: localhost
Target Database: echelon
Date: 10/06/2010 17:48:24
*/

SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for ech_blacklist
-- ----------------------------
DROP TABLE IF EXISTS `ech_blacklist`;
CREATE TABLE `ech_blacklist` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `ip` varchar(24) NOT NULL,
  `active` enum('0','1') DEFAULT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `time_add` int(32) unsigned DEFAULT NULL,
  `admin_id` smallint(6) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ip` (`ip`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for ech_config
-- ----------------------------
DROP TABLE IF EXISTS `ech_config`;
CREATE TABLE `ech_config` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(25) NOT NULL,
  `value` varchar(255) NOT NULL,
  PRIMARY KEY (`id`,`name`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=28 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for ech_games
-- ----------------------------
DROP TABLE IF EXISTS `ech_games`;
CREATE TABLE `ech_games` (
  `id` smallint(8) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `game` varchar(255) NOT NULL,
  `name_short` varchar(255) DEFAULT NULL,
  `num_srvs` smallint(9) NOT NULL,
  `db_host` varchar(255) NOT NULL,
  `db_user` varchar(255) NOT NULL,
  `db_pw` varchar(255) DEFAULT NULL,
  `db_name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for ech_groups
-- ----------------------------
DROP TABLE IF EXISTS `ech_groups`;
CREATE TABLE `ech_groups` (
  `id` smallint(4) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  `display` varchar(255) DEFAULT NULL,
  `premissions` varchar(512) NOT NULL,
  PRIMARY KEY (`id`,`name`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for ech_links
-- ----------------------------
DROP TABLE IF EXISTS `ech_links`;
CREATE TABLE `ech_links` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `url` varchar(255) NOT NULL,
  `name` varchar(80) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for ech_logs
-- ----------------------------
DROP TABLE IF EXISTS `ech_logs`;
CREATE TABLE `ech_logs` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(64) DEFAULT NULL,
  `msg` varchar(255) DEFAULT '',
  `client_id` smallint(5) DEFAULT NULL,
  `user_id` smallint(5) DEFAULT NULL,
  `time_add` int(32) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for ech_permissions
-- ----------------------------
DROP TABLE IF EXISTS `ech_permissions`;
CREATE TABLE `ech_permissions` (
  `id` mediumint(6) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`,`name`)
) ENGINE=MyISAM AUTO_INCREMENT=27 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for ech_plugins
-- ----------------------------
DROP TABLE IF EXISTS `ech_plugins`;
CREATE TABLE `ech_plugins` (
  `id` smallint(8) unsigned NOT NULL AUTO_INCREMENT,
  `game_id` smallint(8) unsigned NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `info` varchar(255) DEFAULT NULL,
  `enabled` enum('0','1') DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for ech_servers
-- ----------------------------
DROP TABLE IF EXISTS `ech_servers`;
CREATE TABLE `ech_servers` (
  `id` int(5) unsigned NOT NULL AUTO_INCREMENT,
  `game` smallint(5) NOT NULL,
  `name` varchar(100) NOT NULL,
  `ip` varchar(15) NOT NULL,
  `pb_active` enum('1','0') NOT NULL,
  `rcon_pass` varchar(50) NOT NULL,
  `rcon_ip` varchar(26) NOT NULL,
  `rcon_port` int(5) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `game` (`game`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for ech_user_keys
-- ----------------------------
DROP TABLE IF EXISTS `ech_user_keys`;
CREATE TABLE `ech_user_keys` (
  `reg_key` varchar(40) NOT NULL,
  `ech_group` smallint(4) NOT NULL,
  `admin_id` smallint(5) unsigned NOT NULL,
  `comment` varchar(500) DEFAULT NULL,
  `time_add` mediumint(24) unsigned DEFAULT NULL,
  `email` varchar(160) NOT NULL,
  `active` enum('0','1') NOT NULL,
  PRIMARY KEY (`reg_key`),
  KEY `email` (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for ech_users
-- ----------------------------
DROP TABLE IF EXISTS `ech_users`;
CREATE TABLE `ech_users` (
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
  KEY `salt` (`salt`)
) ENGINE=MyISAM AUTO_INCREMENT=22 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records 
-- ----------------------------
INSERT INTO `ech_blacklist` VALUES ('1', '255.255.255.255', '1', 'auto', '1276191268', '1');
INSERT INTO `ech_config` VALUES ('1', 'name', 'Development!');
INSERT INTO `ech_config` VALUES ('2', 'num_games', '2');
INSERT INTO `ech_config` VALUES ('3', 'limit_rows', '50');
INSERT INTO `ech_config` VALUES ('4', 'min_pw_len', '8');
INSERT INTO `ech_config` VALUES ('5', 'user_key_expire', '14');
INSERT INTO `ech_config` VALUES ('6', 'email', 'eire32kevin@gmail.com');
INSERT INTO `ech_config` VALUES ('7', 'admin_name', 'Kevin and Jon');
INSERT INTO `ech_config` VALUES ('8', 'https', '0');
INSERT INTO `ech_config` VALUES ('9', 'allow_ie', '1');
INSERT INTO `ech_config` VALUES ('10', 'time_format', 'D, d/m/y (H:i)');
INSERT INTO `ech_config` VALUES ('11', 'time_zone', 'Europe/Dublin');
INSERT INTO `ech_config` VALUES ('12', 'email_header', 'Hello %name%, This is an email from the administrators at %ech_name% Echelon.');
INSERT INTO `ech_config` VALUES ('13', 'email_footer', 'Thanks, the %ech_name% Echelon Team');
INSERT INTO `ech_config` VALUES ('14', 'pw_req_level', '1');
INSERT INTO `ech_config` VALUES ('14', 'pw_req_level_group', '64');
INSERT INTO `ech_groups` VALUES ('1', 'visitor', 'Visitor', '1');
INSERT INTO `ech_groups` VALUES ('2', 'siteadmin', 'Site Admin', '1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30');
INSERT INTO `ech_groups` VALUES ('3', 'senioradmin', 'Senior Admin', '1,2,3,4');
INSERT INTO `ech_groups` VALUES ('4', 'admin', 'Admin', '1,2,3');
INSERT INTO `ech_groups` VALUES ('5', 'mod', 'Moderator', '1,2');
INSERT INTO `ech_links` VALUES ('1', 'http://wiki.bigbrotherbot.net/doku.php/echelon', 'Echelon Help Wiki', 'Documentation for the installation and use of Echelon');
INSERT INTO `ech_links` VALUES ('2', 'http://echelon.bigbrotherbot.net/', 'Echelon Home', 'Home site of Echelon project, check here for development news, updates, and information regarding Echelon');
INSERT INTO `ech_links` VALUES ('3', 'http://eire32designs.com', 'Eire32 Site', 'The developers site');
INSERT INTO `ech_links` VALUES ('4', 'http://www.bigbrotherbot.net/forums/', 'B3 Site', 'Home of bigbrother bot');
INSERT INTO `ech_permissions` VALUES ('1', 'login', 'Allows the user to login');
INSERT INTO `ech_permissions` VALUES ('2', 'clients', 'Allows the user to view the client listing');
INSERT INTO `ech_permissions` VALUES ('3', 'chatlogs', 'Allows the user to view Chatlogs');
INSERT INTO `ech_permissions` VALUES ('4', 'penalties', 'Allows the user to view the Penalty Listing pages');
INSERT INTO `ech_permissions` VALUES ('5', 'admins', 'Allows the user to view the Admins Pages');
INSERT INTO `ech_permissions` VALUES ('6', 'manage_settings', 'Allows the user to Manage Echelon Settings.');
INSERT INTO `ech_permissions` VALUES ('7', 'pbss', 'Allows the user to view PBSS (If Enabled)');
INSERT INTO `ech_permissions` VALUES ('8', 'logs', 'Allows the user to view Logs');
INSERT INTO `ech_permissions` VALUES ('9', 'edit_user', 'Allows the user to edit other Echelon users');
INSERT INTO `ech_permissions` VALUES ('10', 'add_user', 'Allows the user to Add Echelon Users');
INSERT INTO `ech_permissions` VALUES ('11', 'manage_servers', 'Allows the user to Manage Servers');
INSERT INTO `ech_permissions` VALUES ('12', 'ban', 'Allows the user to Ban');
INSERT INTO `ech_permissions` VALUES ('13', 'edit_mask', 'Allows the user to Edit User Level Masks');
INSERT INTO `ech_permissions` VALUES ('14', 'siteadmin', 'Allows the user to add users and control the site blacklist');
INSERT INTO `ech_permissions` VALUES ('15', 'edit_level', 'Allows the user to Change Echelon Levels');
INSERT INTO `ech_permissions` VALUES ('16', 'comment', 'Allows a user to adda comment to a client');
INSERT INTO `ech_permissions` VALUES ('17', 'greeting', 'Allows the user to change the greeting of a client');
INSERT INTO `ech_permissions` VALUES ('18', 'edit_client_level', 'Allows user to change a players B3 level');
INSERT INTO `ech_permissions` VALUES ('19', 'edit_ban', 'Allows user to edit a B3 ban');
INSERT INTO `ech_permissions` VALUES ('20', 'view_ip', 'Allows the user to view players IP addresses');
INSERT INTO `ech_permissions` VALUES ('21', 'view_full_guid', 'Allow the user to view players full GUID');
INSERT INTO `ech_permissions` VALUES ('22', 'view_half_guid', 'Allow the user to view half of the player GUID');
INSERT INTO `ech_permissions` VALUES ('23', 'unban', 'Allows user to remove a B3 Ban');
INSERT INTO `ech_permissions` VALUES ('24', 'edit_xlrstats', 'Allows user to edit a client\'s XLRStats information (hidden, fixed name)');
INSERT INTO `ech_permissions` VALUES ('25', 'ctime', 'Allows user to view CTime information');
INSERT INTO `ech_permissions` VALUES ('26', 'see_update_msg', 'Shows this user the Echelon needs updating message');