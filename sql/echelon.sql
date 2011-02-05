# phpMyAdmin SQL Dump
# version 2.5.7
# http://www.phpmyadmin.net
#
# Host: localhost
# Generation Time: May 23, 2005 at 10:56 PM
# Server version: 4.0.20
# PHP Version: 4.3.8
# 
# Database : `echelon`
# 

# --------------------------------------------------------

#
# Table structure for table `links`
#

CREATE TABLE `links` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(50) NOT NULL default '',
  `link` varchar(100) NOT NULL default '',
  `description` text,
  `level` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 ;

#
# Dumping data for table `links`
#

INSERT INTO `links` VALUES (1, 'B3 Home', 'http://www.bigbrotherbot.com/', 'BigBrotherBot Home', 3);
INSERT INTO `links` VALUES (2, 'B3 Forums', 'http://www.bigbrotherbot.com/forums/', 'Support and Plugin Forums for B3', 3);
INSERT INTO `links` VALUES (3, 'xlr8or', 'http://www.xlr8or.com/', 'Home of xlr8or', 3);

# --------------------------------------------------------

#
# Table structure for table `users`
#

CREATE TABLE `users` (
  `id` int(11) NOT NULL auto_increment,
  `username` varchar(25) NOT NULL default '',
  `password` varchar(32) NOT NULL default '',
  `ech_level` int(11) NOT NULL default '0',
  `b3cod` int(11) default NULL,
  `b3uo` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 ;

#
# Dumping data for table `users`
#

INSERT INTO `users` VALUES (1, 'admin', '21232f297a57a5a743894a0e4a801fc3', 1, 0, 0);
    

