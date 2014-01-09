-- ----------------------------
-- Records of block
-- ----------------------------
INSERT INTO `block` VALUES ('1', 'MAIN_CONTENT');
INSERT INTO `block` VALUES ('2', 'HEADER8');
INSERT INTO `block` VALUES ('17', 'FOOTER');
INSERT INTO `block` VALUES ('35', 'Test With Id5');
INSERT INTO `block` VALUES ('36', 'Test Noty');

-- ----------------------------
-- Records of page
-- ----------------------------
INSERT INTO `page` VALUES ('1', '0', '/', '1');
INSERT INTO `page` VALUES ('2', '1', '/news', '1');

-- ----------------------------
-- Records of page_block
-- ----------------------------
INSERT INTO `page_block` VALUES ('1', '1', '1', 'This is page content [[Model_Page.get_all_pages_uri.2?above=0&less=5]] contained in CONTENT block.  [[Model_Page.get_all_pages_uri.2?above=0&less=2]] ');
INSERT INTO `page_block` VALUES ('2', '2', '1', 'This is news page.');

-- ----------------------------
-- Records of role
-- ----------------------------
INSERT INTO `role` VALUES ('1', 'user', 'Login privileges, granted after account confirmation');
INSERT INTO `role` VALUES ('2', 'admin', 'Administrative user, has access to everything.');

-- ----------------------------
-- Records of template
-- ----------------------------
INSERT INTO `template` VALUES ('1', 'Main Template', 'index.html');
INSERT INTO `template` VALUES ('2', 'Uris', 'snippet/uris.html');

