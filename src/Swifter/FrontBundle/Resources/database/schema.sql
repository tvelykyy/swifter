SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `block`
-- ----------------------------
DROP TABLE IF EXISTS `block`;
CREATE TABLE `block` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Represents page''s field unique identifier.',
  `title` varchar(100) NOT NULL COMMENT 'Represents page''s block title.',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for `page`
-- ----------------------------
DROP TABLE IF EXISTS `page`;
CREATE TABLE `page` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Represent unique page identifier',
  `parent_id` bigint(20) NOT NULL,
  `uri` varchar(200) NOT NULL COMMENT 'Represents page''s full URL.',
  `template_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for `page_block`
-- ----------------------------
DROP TABLE IF EXISTS `page_block`;
CREATE TABLE `page_block` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `page_id` bigint(20) unsigned NOT NULL COMMENT 'Represent page identifier.',
  `block_id` bigint(20) unsigned NOT NULL COMMENT 'Represents block identifier',
  `content` text NOT NULL COMMENT 'Reprents page block content for specific page.',
  PRIMARY KEY (`id`),
  UNIQUE KEY `page_block` (`page_id`,`block_id`),
  KEY `block` (`block_id`),
  CONSTRAINT `block` FOREIGN KEY (`block_id`) REFERENCES `block` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `page` FOREIGN KEY (`page_id`) REFERENCES `page` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='Table represents mapping between page block and specific page.';

-- ----------------------------
-- Table structure for `role`
-- ----------------------------
DROP TABLE IF EXISTS `role`;
CREATE TABLE `role` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(20) NOT NULL,
  `description` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_title` (`title`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for `template`
-- ----------------------------
DROP TABLE IF EXISTS `template`;
CREATE TABLE `template` (
  `id` bigint(20) NOT NULL,
  `title` varchar(100) NOT NULL,
  `path` varchar(200) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for `user`
-- ----------------------------
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `email` varchar(127) NOT NULL,
  `password` varchar(64) NOT NULL,
  `username` varchar(32) NOT NULL DEFAULT '',
  `enabled` bit(1) NOT NULL DEFAULT b'0',
  `last_login` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`email`),
  UNIQUE KEY `uniq_username` (`username`) USING BTREE,
  UNIQUE KEY `uniq_email` (`email`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for `user_role`
-- ----------------------------
DROP TABLE IF EXISTS `user_role`;
CREATE TABLE `user_role` (
  `user_email` varchar(127) NOT NULL,
  `role_id` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`user_email`,`role_id`),
  UNIQUE KEY `unique_row` (`user_email`,`role_id`),
  KEY `role` (`role_id`),
  CONSTRAINT `role` FOREIGN KEY (`role_id`) REFERENCES `role` (`id`),
  CONSTRAINT `user` FOREIGN KEY (`user_email`) REFERENCES `user` (`email`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for `user_token`
-- ----------------------------
DROP TABLE IF EXISTS `user_token`;
CREATE TABLE `user_token` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_email` varchar(127) NOT NULL,
  `user_agent` varchar(40) NOT NULL,
  `token` varchar(40) NOT NULL,
  `created` int(10) unsigned NOT NULL,
  `expires` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_token` (`token`),
  KEY `fk_user_email` (`user_email`)
) ENGINE=InnoDB AUTO_INCREMENT=92 DEFAULT CHARSET=utf8;

