/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50535
Source Host           : localhost:3306
Source Database       : swifter

Target Server Type    : MYSQL
Target Server Version : 50535
File Encoding         : 65001

Date: 2014-04-09 21:12:51
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `block`
-- ----------------------------
DROP TABLE IF EXISTS `block`;
CREATE TABLE `block` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Represents page''s field unique identifier.',
  `title` varchar(50) NOT NULL COMMENT 'Represents page''s block title.',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Records of block
-- ----------------------------
INSERT INTO `block` VALUES ('1', 'MAIN_CONTENT');
INSERT INTO `block` VALUES ('2', 'TITLE');
INSERT INTO `block` VALUES ('3', 'FOOTER');
INSERT INTO `block` VALUES ('7', 'KEYWORDS');
INSERT INTO `block` VALUES ('16', 'DESCRIPTION');
INSERT INTO `block` VALUES ('18', 'Mixmarket54');
INSERT INTO `block` VALUES ('19', 'Begun');
INSERT INTO `block` VALUES ('21', 'Tizer');

-- ----------------------------
-- Table structure for `page`
-- ----------------------------
DROP TABLE IF EXISTS `page`;
CREATE TABLE `page` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Represent unique page identifier',
  `name` varchar(50) NOT NULL,
  `uri` varchar(200) NOT NULL COMMENT 'Represents page''s full URL.',
  `parent_id` bigint(20) DEFAULT NULL,
  `template_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of page
-- ----------------------------
INSERT INTO `page` VALUES ('1', 'Головна сторінка', '/', null, '1');
INSERT INTO `page` VALUES ('2', 'Блок Новин', '/news', '1', '1');
INSERT INTO `page` VALUES ('3', 'Супер перша новина', '/news/first', '2', '1');

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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='Table represents mapping between page block and specific page.';

-- ----------------------------
-- Records of page_block
-- ----------------------------
INSERT INTO `page_block` VALUES ('1', '1', '1', 'Так-Так. This is page content [[DEV_TEST_PAGES]] contained in CONTENT block.');
INSERT INTO `page_block` VALUES ('2', '2', '1', 'This is news page.');
INSERT INTO `page_block` VALUES ('3', '1', '2', 'Заголовок кирилиця.');
INSERT INTO `page_block` VALUES ('4', '3', '3', 'This is super cool footer.');
INSERT INTO `page_block` VALUES ('5', '2', '3', 'Medium footer.');

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
-- Records of role
-- ----------------------------
INSERT INTO `role` VALUES ('1', 'ROLE_USER', 'Login privileges, granted after registration.');
INSERT INTO `role` VALUES ('2', 'ROLE_ADMIN', 'Administrative user.');

-- ----------------------------
-- Table structure for `snippet`
-- ----------------------------
DROP TABLE IF EXISTS `snippet`;
CREATE TABLE `snippet` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL,
  `service` varchar(200) NOT NULL,
  `method` varchar(100) NOT NULL,
  `template_id` int(11) unsigned NOT NULL,
  `params` varchar(500) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `templated_id_idx` (`template_id`),
  CONSTRAINT `template_id_fk` FOREIGN KEY (`template_id`) REFERENCES `template` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of snippet
-- ----------------------------
INSERT INTO `snippet` VALUES ('1', 'DEV_TEST_PAGES', 'front.service.devtest', 'getPages', '2', '{\"offset\":0, \"limit\":5}');

-- ----------------------------
-- Table structure for `template`
-- ----------------------------
DROP TABLE IF EXISTS `template`;
CREATE TABLE `template` (
  `id` int(11) unsigned NOT NULL,
  `title` varchar(100) NOT NULL,
  `path` varchar(200) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of template
-- ----------------------------
INSERT INTO `template` VALUES ('1', 'Main Template', 'SwifterFrontBundle:DevTest:index.html.twig');
INSERT INTO `template` VALUES ('2', 'Uris', 'SwifterFrontBundle:DevTest:pages.html.twig');

-- ----------------------------
-- Table structure for `user`
-- ----------------------------
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(127) NOT NULL,
  `password` char(128) NOT NULL,
  `name` varchar(32) NOT NULL DEFAULT '',
  `enabled` bit(1) NOT NULL DEFAULT b'0',
  `last_login` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_email` (`email`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Records of user
-- ----------------------------
INSERT INTO `user` VALUES ('1', 'admin@m.com', 'c7ad44cbad762a5da0a452f9e854fdc1e0e7a52a38015f23f3eab1d80b931dd472634dfac71cd34ebc35d16ab7fb8a90c81f975113d6c7538dc69dd8de9077ec', 'Cmser Admin 1', '', null);
INSERT INTO `user` VALUES ('2', 'user@m.com', 'c7ad44cbad762a5da0a452f9e854fdc1e0e7a52a38015f23f3eab1d80b931dd472634dfac71cd34ebc35d16ab7fb8a90c81f975113d6c7538dc69dd8de9077ec', 'Cmser User 1', '', null);

-- ----------------------------
-- Table structure for `user_role`
-- ----------------------------
DROP TABLE IF EXISTS `user_role`;
CREATE TABLE `user_role` (
  `user_id` int(10) unsigned NOT NULL,
  `role_id` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`user_id`,`role_id`),
  UNIQUE KEY `unique_row` (`user_id`,`role_id`) USING BTREE,
  KEY `role` (`role_id`) USING BTREE,
  CONSTRAINT `user_role_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `role` (`id`),
  CONSTRAINT `user_role_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Records of user_role
-- ----------------------------
INSERT INTO `user_role` VALUES ('2', '1');
INSERT INTO `user_role` VALUES ('1', '2');
