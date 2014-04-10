/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50535
Source Host           : localhost:3306
Source Database       : swifter_test

Target Server Type    : MYSQL
Target Server Version : 50535
File Encoding         : 65001

Date: 2014-04-10 14:07:24
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `block`
-- ----------------------------
DROP TABLE IF EXISTS `block`;
CREATE TABLE `block` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Represents page''s field unique identifier.',
  `title` varchar(100) NOT NULL COMMENT 'Represents page''s block title.',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Records of block
-- ----------------------------
INSERT INTO `block` VALUES ('1', 'MAIN_CONTENT');
INSERT INTO `block` VALUES ('2', 'TITLE');
INSERT INTO `block` VALUES ('3', 'FOOTER');

-- ----------------------------
-- Table structure for `page`
-- ----------------------------
DROP TABLE IF EXISTS `page`;
CREATE TABLE `page` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Represent unique page identifier',
  `name` varchar(50) NOT NULL,
  `parent_id` bigint(20) DEFAULT NULL,
  `uri` varchar(200) NOT NULL COMMENT 'Represents page''s full URL.',
  `template_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of page
-- ----------------------------
INSERT INTO `page` VALUES ('1', '', null, '/', '1');
INSERT INTO `page` VALUES ('2', '', '1', '/news', '1');
INSERT INTO `page` VALUES ('3', '', '2', '/news/first', '1');

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
INSERT INTO `page_block` VALUES ('1', '1', '1', 'Yes-Yes. This is page content [[DEV_TEST_PAGES]] contained in CONTENT block.');
INSERT INTO `page_block` VALUES ('2', '2', '1', 'This is a news page.');
INSERT INTO `page_block` VALUES ('3', '1', '2', 'Заголовок кирилиця і буква І!');
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
INSERT INTO `role` VALUES ('1', 'user', 'Login privileges, granted after account confirmation');
INSERT INTO `role` VALUES ('2', 'admin', 'Administrative user, has access to everything.');

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
  `password` varchar(64) NOT NULL,
  `name` varchar(32) NOT NULL DEFAULT '',
  `enabled` bit(1) NOT NULL DEFAULT b'0',
  `last_login` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_email` (`email`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Records of user
-- ----------------------------
INSERT INTO `user` VALUES ('1', 'cmser_admin_1@mailinator.com', '$2a$10$hYeVqGTWHfPeup7iZ98eXeSOe1sIjVYy7yp95FePKnc0KV1H4Ux.K', 'Cmser Admin 1', '', null);
INSERT INTO `user` VALUES ('2', 'cmser_user_1@mailinator.com', '$2a$10$hYeVqGTWHfPeup7iZ98eXeSOe1sIjVYy7yp95FePKnc0KV1H4Ux.K', 'Cmser User 1', '', null);

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
-- Records of user_role
-- ----------------------------
INSERT INTO `user_role` VALUES ('cmser_admin_1@mailinator.com', '1');
INSERT INTO `user_role` VALUES ('cmser_user_1@mailinator.com', '2');

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

-- ----------------------------
-- Records of user_token
-- ----------------------------
INSERT INTO `user_token` VALUES ('44', 'dummy@mail.com', 'da39a3ee5e6b4b0d3255bfef95601890afd80709', 'df4ed7297a2db98cec927647f46505d74ef75f8f', '0', '1385740031');
INSERT INTO `user_token` VALUES ('46', 'tavelyky@gmail.com', '30943b39e0c5b981478e480b9277538cb86ca4a4', '5f49c1c6e8115675521c74c6b0cd5269d245082e', '0', '1385743124');
INSERT INTO `user_token` VALUES ('55', 'tavelyky@gmail.com', '5c89949a3bc004f86ee7912d7c6d086c4feba7c7', '9ac6cee13d6c3eb5efe56796a4dec6093d55d0da', '0', '1386332613');
INSERT INTO `user_token` VALUES ('56', 'tavelyky@gmail.com', '5c89949a3bc004f86ee7912d7c6d086c4feba7c7', 'e2c668367b98d0ea152b171b03a5608717cc1d6f', '0', '1386333025');
INSERT INTO `user_token` VALUES ('57', 'tavelyky@gmail.com', '5c89949a3bc004f86ee7912d7c6d086c4feba7c7', '23947fea87fa423e0b494122dae4d2df006d70f9', '0', '1386333026');
INSERT INTO `user_token` VALUES ('58', 'tavelyky@gmail.com', '5c89949a3bc004f86ee7912d7c6d086c4feba7c7', '00aa130a01fc51d4692accece253600f6a7e48b0', '0', '1386333028');
INSERT INTO `user_token` VALUES ('59', 'tavelyky@gmail.com', '5c89949a3bc004f86ee7912d7c6d086c4feba7c7', '9d30c9d93966848f4174272f926e0d4cbd9f32c7', '0', '1386333029');
INSERT INTO `user_token` VALUES ('60', 'tavelyky@gmail.com', '5c89949a3bc004f86ee7912d7c6d086c4feba7c7', '25e8cffdb9ce733a03e219519fdbee143b274233', '0', '1386333086');
INSERT INTO `user_token` VALUES ('61', 'cmser_user_1@mailinator.com', '5c89949a3bc004f86ee7912d7c6d086c4feba7c7', 'c8249711138409c16da9a5d9f481b7962eb1f563', '0', '1386333413');
INSERT INTO `user_token` VALUES ('63', 'cmser_user_1@mailinator.com', '5c89949a3bc004f86ee7912d7c6d086c4feba7c7', 'f8dd2fdb985b736c4d9d0146f54507afc176c497', '0', '1386334096');
INSERT INTO `user_token` VALUES ('66', 'cmser_user_1@mailinator.com', '5c89949a3bc004f86ee7912d7c6d086c4feba7c7', 'f9b1dbbac4bcc3ffaa0c2e5290d75c5dc5972ba7', '0', '1386339730');
INSERT INTO `user_token` VALUES ('68', 'cmser_admin_1@mailinator.com', '5c89949a3bc004f86ee7912d7c6d086c4feba7c7', '2567389eb169cf16035f73fedc859b2e24eb4887', '0', '1386342789');
INSERT INTO `user_token` VALUES ('70', 'tavelyky@gmail.com', '40111fb86247032d0422c35e58c32ab3d02bbafb', '3d8a366b67410c46c5e01d6b30dc28aa2e4add0e', '0', '1386929049');
INSERT INTO `user_token` VALUES ('85', 'cmser_admin_1@mailinator.com', 'aa7f970efc771c1c4cab4ab84060e62c11c44f3d', '29ef0f92df2979eb75db78ac40969319cd588530', '0', '1387460815');
INSERT INTO `user_token` VALUES ('91', 'cmser_admin_1@mailinator.com', '40111fb86247032d0422c35e58c32ab3d02bbafb', '1d2482fd7f09e1b8bdd9088d77cf94e10d1e4b86', '0', '1387964909');
