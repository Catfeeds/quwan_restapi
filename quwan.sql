/*
Navicat MySQL Data Transfer

Source Server         : 119.29.87.252
Source Server Version : 50636
Source Host           : 119.29.87.252:3306
Source Database       : quwan

Target Server Type    : MYSQL
Target Server Version : 50636
File Encoding         : 65001

Date: 2018-01-22 10:57:16
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for qw_admin
-- ----------------------------
DROP TABLE IF EXISTS `qw_admin`;
CREATE TABLE `qw_admin` (
  `admin_id` int(11) NOT NULL AUTO_INCREMENT,
  `user` varchar(225) NOT NULL,
  `head` varchar(255) NOT NULL COMMENT '头像',
  `sex` tinyint(1) NOT NULL COMMENT '0保密1男，2女',
  `birthday` int(10) NOT NULL COMMENT '生日',
  `phone` varchar(20) NOT NULL COMMENT '电话',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1有效，0无效',
  `qq` varchar(20) NOT NULL COMMENT 'QQ',
  `email` varchar(255) NOT NULL COMMENT '邮箱',
  `password` varchar(32) NOT NULL,
  `t` int(10) unsigned NOT NULL COMMENT '注册时间',
  PRIMARY KEY (`admin_id`)
) ENGINE=MyISAM AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COMMENT='后台用户表';

-- ----------------------------
-- Records of qw_admin
-- ----------------------------
INSERT INTO `qw_admin` VALUES ('1', 'admin', '', '1', '1420128000', 'admin', '1', '331349451', '273719650@qq.com', 'c64f7dace6f08e6a326b1ec465cf3e95', '1442505600');

-- ----------------------------
-- Table structure for qw_admin_log
-- ----------------------------
DROP TABLE IF EXISTS `qw_admin_log`;
CREATE TABLE `qw_admin_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) DEFAULT '0',
  `name` varchar(100) NOT NULL,
  `t` int(10) NOT NULL,
  `ip` varchar(16) NOT NULL,
  `log` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1084 DEFAULT CHARSET=utf8mb4 COMMENT='后台用户日志表';

-- ----------------------------
-- Records of qw_admin_log
-- ----------------------------

-- ----------------------------
-- Table structure for qw_admin_mobile
-- ----------------------------
DROP TABLE IF EXISTS `qw_admin_mobile`;
CREATE TABLE `qw_admin_mobile` (
  `mobile_id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) NOT NULL DEFAULT '0',
  `mobile` varchar(20) NOT NULL COMMENT '手机号码',
  `add_time` int(11) NOT NULL DEFAULT '0' COMMENT '时间',
  `mobile_status` tinyint(4) DEFAULT '1' COMMENT '1有效，0无效',
  `mobile_code` varchar(10) NOT NULL,
  PRIMARY KEY (`mobile_id`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of qw_admin_mobile
-- ----------------------------

-- ----------------------------
-- Table structure for qw_admin_shop
-- ----------------------------
DROP TABLE IF EXISTS `qw_admin_shop`;
CREATE TABLE `qw_admin_shop` (
  `shop_id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  PRIMARY KEY (`shop_id`,`admin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='管理用户和商家对应表';

-- ----------------------------
-- Records of qw_admin_shop
-- ----------------------------

-- ----------------------------
-- Table structure for qw_adv
-- ----------------------------
DROP TABLE IF EXISTS `qw_adv`;
CREATE TABLE `qw_adv` (
  `adv_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `adv_title` varchar(72) COLLATE utf8mb4_bin NOT NULL COMMENT '标题',
  `adv_url` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '广告地址',
  `adv_weight` int(11) DEFAULT '1' COMMENT '排序权重小在前',
  `adv_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1链接，2内页',
  `adv_img` varchar(255) COLLATE utf8mb4_bin NOT NULL,
  `adv_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0禁用,1启用',
  `adv_created_at` int(11) DEFAULT '0',
  `adv_updated_at` int(11) DEFAULT '0',
  `adv_content` text COLLATE utf8mb4_bin COMMENT '图文内容',
  PRIMARY KEY (`adv_id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin COMMENT='广告表';

-- ----------------------------
-- Records of qw_adv
-- ----------------------------
-- ----------------------------
-- Table structure for qw_attractions
-- ----------------------------
DROP TABLE IF EXISTS `qw_attractions`;
CREATE TABLE `qw_attractions` (
  `attractions_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `shop_id` int(11) NOT NULL DEFAULT '0' COMMENT '商家id',
  `attractions_name` varchar(32) NOT NULL COMMENT '景点名称',
  `attractions_address` varchar(255) NOT NULL COMMENT '地址',
  `attractions_phone` varchar(32) NOT NULL COMMENT '电话',
  `attractions_price` decimal(10,2) DEFAULT '0.00' COMMENT '价格',
  `attractions_intro` text NOT NULL COMMENT '介绍html_specialchars',
  `attractions_score` decimal(10,1) DEFAULT '0.0' COMMENT '评分',
  `attractions_evaluation` int(10) DEFAULT '0' COMMENT '评价',
  `attractions_is_refund` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否可退货,0否,1是',
  `attractions_lon` varchar(64) NOT NULL DEFAULT '' COMMENT '经度',
  `attractions_lat` varchar(64) NOT NULL DEFAULT '' COMMENT '纬度',
  `attractions_geohash` varchar(255) DEFAULT '' COMMENT '通过经纬度换算得到的字符串索引',
  `attractions_open_time` varchar(50) DEFAULT NULL COMMENT '开放时间',
  `attractions_start_at` int(11) DEFAULT '0' COMMENT '开放时间',
  `attractions_end_at` int(11) DEFAULT '0' COMMENT '结束时间',
  `attractions_sort` int(10) DEFAULT '0' COMMENT '排序(从小到大)',
  `attractions_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0禁用,1启用,-1删除',
  `attractions_created_at` int(11) DEFAULT '0',
  `attractions_updated_at` int(11) DEFAULT '0',
  `attractions_suggest` varchar(64) DEFAULT '' COMMENT '建议游玩',
  `attractions_sales_num` int(10) DEFAULT '0' COMMENT '销售数(目的地详情页需要用)',
  `attractions_score_num` int(11) DEFAULT '0' COMMENT '景点评论数',
  PRIMARY KEY (`attractions_id`)
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8mb4 COMMENT='景点表';

-- ----------------------------
-- Records of qw_attractions
-- ----------------------------
-- ----------------------------
-- Table structure for qw_auth_group
-- ----------------------------
DROP TABLE IF EXISTS `qw_auth_group`;
CREATE TABLE `qw_auth_group` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `title` char(100) NOT NULL DEFAULT '',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `rules` varchar(500) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COMMENT='用户组表';

-- ----------------------------
-- Records of qw_auth_group
-- ----------------------------
INSERT INTO `qw_auth_group` VALUES ('1', '超级管理员', '1', '1,2,58,59,60,61,62,3,56,4,6,5,7,8,9,10,51,52,53,57,11,54,12,13,14,15,16,17,18,19,20,21,22,23,48,158,159,49,50,55');
INSERT INTO `qw_auth_group` VALUES ('2', '商家组', '1', '1,134,135,136,137,48,111,112,158,159,50,160,124,125,126,127,128,129,131,132,133,178,145,146,147,148,149,150,151,153,154,155,156,157');
INSERT INTO `qw_auth_group` VALUES ('3', '平台管理', '1', '1,48,158,159,49,50,55,108,109,110,101,102,103,104,68,70,71,72,73,74,75,76,177,69,113,114,115,171,172,173,174,175,138,139,140,141,142,143,144,179,124,125,126,127,130,131,132,133,161,162,163,164,165,145,146,148,149,152,153,154,155,156,157,116,117,118,119,120,121,122,123,166,167,168,169,170,83,84,85,86,87,88,89,90,91,92,93,94,95,96,97,98,99,100,77,78,79,80,81,176,105,106,107,82');

-- ----------------------------
-- Table structure for qw_auth_group_access
-- ----------------------------
DROP TABLE IF EXISTS `qw_auth_group_access`;
CREATE TABLE `qw_auth_group_access` (
  `admin_id` mediumint(8) unsigned NOT NULL,
  `group_id` mediumint(8) unsigned NOT NULL,
  UNIQUE KEY `uid_group_id` (`admin_id`,`group_id`),
  KEY `uid` (`admin_id`),
  KEY `group_id` (`group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COMMENT='后台用户和用户组对应表';

-- ----------------------------
-- Records of qw_auth_group_access
-- ----------------------------
INSERT INTO `qw_auth_group_access` VALUES ('1', '1');
INSERT INTO `qw_auth_group_access` VALUES ('2', '2');
INSERT INTO `qw_auth_group_access` VALUES ('3', '3');
INSERT INTO `qw_auth_group_access` VALUES ('4', '3');
INSERT INTO `qw_auth_group_access` VALUES ('5', '2');
INSERT INTO `qw_auth_group_access` VALUES ('6', '2');
INSERT INTO `qw_auth_group_access` VALUES ('13', '2');
INSERT INTO `qw_auth_group_access` VALUES ('14', '2');
INSERT INTO `qw_auth_group_access` VALUES ('15', '2');
INSERT INTO `qw_auth_group_access` VALUES ('16', '2');
INSERT INTO `qw_auth_group_access` VALUES ('17', '2');
INSERT INTO `qw_auth_group_access` VALUES ('18', '2');
INSERT INTO `qw_auth_group_access` VALUES ('19', '2');
INSERT INTO `qw_auth_group_access` VALUES ('20', '2');
INSERT INTO `qw_auth_group_access` VALUES ('21', '2');
INSERT INTO `qw_auth_group_access` VALUES ('22', '2');
INSERT INTO `qw_auth_group_access` VALUES ('23', '2');
INSERT INTO `qw_auth_group_access` VALUES ('24', '2');
INSERT INTO `qw_auth_group_access` VALUES ('25', '2');
INSERT INTO `qw_auth_group_access` VALUES ('26', '2');
INSERT INTO `qw_auth_group_access` VALUES ('27', '2');
INSERT INTO `qw_auth_group_access` VALUES ('28', '2');

-- ----------------------------
-- Table structure for qw_auth_rule
-- ----------------------------
DROP TABLE IF EXISTS `qw_auth_rule`;
CREATE TABLE `qw_auth_rule` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL,
  `name` char(255) NOT NULL DEFAULT '',
  `title` char(20) NOT NULL DEFAULT '',
  `icon` varchar(255) NOT NULL,
  `type` tinyint(1) NOT NULL DEFAULT '1',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `condition` char(100) NOT NULL DEFAULT '',
  `islink` tinyint(1) NOT NULL DEFAULT '1',
  `o` int(11) NOT NULL COMMENT '排序',
  `tips` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=180 DEFAULT CHARSET=utf8mb4 COMMENT='权限菜单规则表';

-- ----------------------------
-- Records of qw_auth_rule
-- ----------------------------
INSERT INTO `qw_auth_rule` VALUES ('1', '0', 'Index/index', '控制台', '', '1', '1', '', '1', '1', '友情提示：经常查看操作日志，发现异常以便及时追查原因。');
INSERT INTO `qw_auth_rule` VALUES ('2', '0', '', '系统设置', '', '1', '1', '', '1', '2', '');
INSERT INTO `qw_auth_rule` VALUES ('3', '2', 'Setting/setting', '网站设置', '', '1', '1', '', '1', '3', '这是网站设置的提示。');
INSERT INTO `qw_auth_rule` VALUES ('4', '2', 'Menu/index', '后台菜单', '', '1', '1', '', '1', '4', '');
INSERT INTO `qw_auth_rule` VALUES ('5', '2', 'Menu/add', '新增菜单', '', '1', '1', '', '1', '5', '');
INSERT INTO `qw_auth_rule` VALUES ('6', '4', 'Menu/edit', '编辑菜单', '', '1', '1', '', '0', '6', '');
INSERT INTO `qw_auth_rule` VALUES ('7', '2', 'Menu/update', '保存菜单', '', '1', '1', '', '0', '7', '');
INSERT INTO `qw_auth_rule` VALUES ('8', '2', 'Menu/del', '删除菜单', '', '1', '1', '', '0', '8', '');
INSERT INTO `qw_auth_rule` VALUES ('9', '2', 'Database/backup', '数据库备份', '', '1', '1', '', '1', '9', '');
INSERT INTO `qw_auth_rule` VALUES ('10', '9', 'Database/recovery', '数据库还原', '', '1', '1', '', '0', '10', '');
INSERT INTO `qw_auth_rule` VALUES ('11', '2', 'Update/update', '在线升级', '', '1', '1', '', '0', '11', '');
INSERT INTO `qw_auth_rule` VALUES ('12', '2', 'Update/devlog', '开发日志', '', '1', '1', '', '0', '12', '');
INSERT INTO `qw_auth_rule` VALUES ('13', '0', '', '用户及组', '', '1', '1', '', '1', '13', '');
INSERT INTO `qw_auth_rule` VALUES ('14', '13', 'Adminuser/index', '用户管理', '', '1', '1', '', '1', '14', '');
INSERT INTO `qw_auth_rule` VALUES ('15', '13', 'Adminuser/add', '新增用户', '', '1', '1', '', '1', '15', '');
INSERT INTO `qw_auth_rule` VALUES ('16', '13', 'Adminuser/edit', '编辑用户', '', '1', '1', '', '0', '16', '');
INSERT INTO `qw_auth_rule` VALUES ('17', '13', 'Adminuser/update', '保存用户', '', '1', '1', '', '0', '17', '');
INSERT INTO `qw_auth_rule` VALUES ('18', '13', 'Adminuser/del', '删除用户', '', '1', '1', '', '0', '18', '');
INSERT INTO `qw_auth_rule` VALUES ('19', '13', 'Group/index', '用户组管理', '', '1', '1', '', '1', '19', '');
INSERT INTO `qw_auth_rule` VALUES ('20', '13', 'Group/add', '新增用户组', '', '1', '1', '', '1', '20', '');
INSERT INTO `qw_auth_rule` VALUES ('21', '13', 'Group/edit', '编辑用户组', '', '1', '1', '', '0', '21', '');
INSERT INTO `qw_auth_rule` VALUES ('22', '13', 'Group/update', '保存用户组', '', '1', '1', '', '0', '22', '');
INSERT INTO `qw_auth_rule` VALUES ('23', '13', 'Group/del', '删除用户组', '', '1', '1', '', '0', '23', '');
INSERT INTO `qw_auth_rule` VALUES ('48', '0', 'Personal/index', '个人中心', '', '1', '1', '', '1', '101', '');
INSERT INTO `qw_auth_rule` VALUES ('49', '48', 'Personal/profile', '个人资料', '', '1', '1', '', '1', '49', '');
INSERT INTO `qw_auth_rule` VALUES ('50', '48', 'Logout/index', '退出', '', '1', '1', '', '1', '50', '');
INSERT INTO `qw_auth_rule` VALUES ('51', '9', 'Database/export', '备份', '', '1', '1', '', '0', '51', '');
INSERT INTO `qw_auth_rule` VALUES ('52', '9', 'Database/optimize', '数据优化', '', '1', '1', '', '0', '52', '');
INSERT INTO `qw_auth_rule` VALUES ('53', '9', 'Database/repair', '修复表', '', '1', '1', '', '0', '53', '');
INSERT INTO `qw_auth_rule` VALUES ('54', '11', 'Update/updating', '升级安装', '', '1', '1', '', '0', '54', '');
INSERT INTO `qw_auth_rule` VALUES ('55', '48', 'Personal/update', '资料保存', '', '1', '1', '', '0', '55', '');
INSERT INTO `qw_auth_rule` VALUES ('56', '3', 'Setting/update', '设置保存', '', '1', '1', '', '0', '56', '');
INSERT INTO `qw_auth_rule` VALUES ('57', '9', 'Database/del', '备份删除', '', '1', '1', '', '0', '57', '');
INSERT INTO `qw_auth_rule` VALUES ('58', '2', 'variable/index', '自定义变量', '', '1', '1', '', '1', '0', '');
INSERT INTO `qw_auth_rule` VALUES ('59', '58', 'variable/add', '新增变量', '', '1', '1', '', '0', '0', '');
INSERT INTO `qw_auth_rule` VALUES ('60', '58', 'variable/edit', '编辑变量', '', '1', '1', '', '0', '0', '');
INSERT INTO `qw_auth_rule` VALUES ('61', '58', 'variable/update', '保存变量', '', '1', '1', '', '0', '0', '');
INSERT INTO `qw_auth_rule` VALUES ('62', '58', 'variable/del', '删除变量', '', '1', '1', '', '0', '0', '');
INSERT INTO `qw_auth_rule` VALUES ('68', '0', '', '首页管理', '', '1', '1', '', '1', '104', '');
INSERT INTO `qw_auth_rule` VALUES ('69', '68', 'HomePage/index', '首页管理', '', '1', '1', '', '1', '100', '');
INSERT INTO `qw_auth_rule` VALUES ('70', '68', 'HomePage/upHomePage', '首页管理update', '', '1', '1', '', '0', '0', '');
INSERT INTO `qw_auth_rule` VALUES ('71', '68', 'HomePage/update', '首页管理updateAll', '', '1', '1', '', '0', '0', '');
INSERT INTO `qw_auth_rule` VALUES ('72', '68', 'HomePage/getList', '首页管理获取数据', '', '1', '1', '', '0', '0', '');
INSERT INTO `qw_auth_rule` VALUES ('73', '68', 'Flash/index', '首页广告位', '', '1', '1', '', '0', '0', '');
INSERT INTO `qw_auth_rule` VALUES ('74', '68', 'Flash/add', '首页广告位新增', '', '1', '1', '', '0', '0', '');
INSERT INTO `qw_auth_rule` VALUES ('75', '68', 'Flash/edit', '首页广告位编辑', '', '1', '1', '', '0', '0', '');
INSERT INTO `qw_auth_rule` VALUES ('76', '68', 'Flash/update', '首页广告update', '', '1', '1', '', '0', '0', '');
INSERT INTO `qw_auth_rule` VALUES ('77', '0', '', '目的地管理', '', '1', '1', '', '1', '113', '');
INSERT INTO `qw_auth_rule` VALUES ('78', '77', 'Destination/index', '目的地', '', '1', '1', '', '1', '0', '');
INSERT INTO `qw_auth_rule` VALUES ('79', '77', 'Destination/add', '目的地新增', '', '1', '1', '', '0', '0', '');
INSERT INTO `qw_auth_rule` VALUES ('80', '77', 'Destination/edit', '目的地编辑', '', '1', '1', '', '0', '0', '');
INSERT INTO `qw_auth_rule` VALUES ('81', '77', 'Destination/update', '目的地update', '', '1', '1', '', '0', '0', '');
INSERT INTO `qw_auth_rule` VALUES ('82', '0', 'Facebook/index', '用户反馈', '', '1', '1', '', '1', '115', '');
INSERT INTO `qw_auth_rule` VALUES ('83', '0', '', '餐饮管理', '', '1', '1', '', '1', '111', '');
INSERT INTO `qw_auth_rule` VALUES ('84', '83', 'Hall/index', '餐饮', '', '1', '1', '', '1', '0', '');
INSERT INTO `qw_auth_rule` VALUES ('85', '83', 'Hall/add', '餐饮新增', '', '1', '1', '', '0', '0', '');
INSERT INTO `qw_auth_rule` VALUES ('86', '83', 'Hall/edit', '餐饮编辑', '', '1', '1', '', '0', '0', '');
INSERT INTO `qw_auth_rule` VALUES ('87', '83', 'Hall/del', '餐饮删除', '', '1', '1', '', '0', '0', '');
INSERT INTO `qw_auth_rule` VALUES ('88', '83', 'Hall/update', '餐饮update', '', '1', '1', '', '0', '0', '');
INSERT INTO `qw_auth_rule` VALUES ('89', '83', 'Hall/replay', '餐饮评论', '', '1', '1', '', '0', '0', '');
INSERT INTO `qw_auth_rule` VALUES ('90', '83', 'Hall/replay_del', '餐饮评论删除', '', '1', '1', '', '0', '0', '');
INSERT INTO `qw_auth_rule` VALUES ('91', '83', 'Hall/replay_submit', '餐饮评论回复', '', '1', '1', '', '0', '0', '');
INSERT INTO `qw_auth_rule` VALUES ('92', '0', '', '酒店管理', '', '1', '1', '', '1', '112', '');
INSERT INTO `qw_auth_rule` VALUES ('93', '92', 'Hotel/index', '酒店', '', '1', '1', '', '1', '0', '');
INSERT INTO `qw_auth_rule` VALUES ('94', '92', 'Hotel/add', '酒店增加', '', '1', '1', '', '0', '0', '');
INSERT INTO `qw_auth_rule` VALUES ('95', '92', 'Hotel/edit', '酒店编辑', '', '1', '1', '', '0', '0', '');
INSERT INTO `qw_auth_rule` VALUES ('96', '92', 'Hotel/del', '酒店删除', '', '1', '1', '', '0', '0', '');
INSERT INTO `qw_auth_rule` VALUES ('97', '92', 'Hotel/update', '酒店update', '', '1', '1', '', '0', '0', '');
INSERT INTO `qw_auth_rule` VALUES ('98', '92', 'Hotel/replay', '酒店评论', '', '1', '1', '', '0', '0', '');
INSERT INTO `qw_auth_rule` VALUES ('99', '92', 'Hotel/replay_del', '酒店评论删除', '', '1', '1', '', '0', '0', '');
INSERT INTO `qw_auth_rule` VALUES ('100', '92', 'Hotel/replay_submit', '酒店评论回复', '', '1', '1', '', '0', '0', '');
INSERT INTO `qw_auth_rule` VALUES ('101', '0', '', '用户管理', '', '1', '1', '', '1', '103', '');
INSERT INTO `qw_auth_rule` VALUES ('102', '101', 'Member/index', '用户编辑', '', '1', '1', '', '1', '0', '');
INSERT INTO `qw_auth_rule` VALUES ('103', '101', 'Member/edit', '会员编辑', '', '1', '1', '', '0', '0', '');
INSERT INTO `qw_auth_rule` VALUES ('104', '101', 'Member/update', '会员update', '', '1', '1', '', '0', '0', '');
INSERT INTO `qw_auth_rule` VALUES ('105', '0', '', '订单管理', '', '1', '1', '', '1', '114', '');
INSERT INTO `qw_auth_rule` VALUES ('106', '105', 'Order/index', '订单', '', '1', '1', '', '1', '0', '');
INSERT INTO `qw_auth_rule` VALUES ('107', '105', 'Order/replay_submit', '订单评论回复', '', '1', '1', '', '0', '0', '');
INSERT INTO `qw_auth_rule` VALUES ('108', '0', '', '平台财务统计', '', '1', '1', '', '1', '102', '');
INSERT INTO `qw_auth_rule` VALUES ('109', '108', 'Orderstat/index', '财务统计', '', '1', '1', '', '1', '0', '');
INSERT INTO `qw_auth_rule` VALUES ('110', '108', 'Orderstat/tongji', '系统统计', '', '1', '1', '', '1', '0', '');
INSERT INTO `qw_auth_rule` VALUES ('111', '48', 'personal/profile_shop', '商家资料', '', '1', '1', '', '1', '0', '');
INSERT INTO `qw_auth_rule` VALUES ('112', '48', 'Personal/update_shop', '商家资料update', '', '1', '1', '', '0', '0', '');
INSERT INTO `qw_auth_rule` VALUES ('113', '0', '', '红包控制', '', '1', '1', '', '1', '105', '');
INSERT INTO `qw_auth_rule` VALUES ('114', '113', 'RedSetting/index', '红包setting', '', '1', '1', '', '1', '0', '');
INSERT INTO `qw_auth_rule` VALUES ('115', '113', 'RedSetting/update', '红包update', '', '1', '1', '', '0', '0', '');
INSERT INTO `qw_auth_rule` VALUES ('116', '0', '', '路线管理', '', '1', '1', '', '1', '110', '');
INSERT INTO `qw_auth_rule` VALUES ('117', '116', 'route/index', '路线', '', '1', '1', '', '1', '0', '');
INSERT INTO `qw_auth_rule` VALUES ('118', '116', 'route/add', '路线新增', '', '1', '1', '', '0', '0', '');
INSERT INTO `qw_auth_rule` VALUES ('119', '116', 'route/edit', '路线编辑', '', '1', '1', '', '0', '0', '');
INSERT INTO `qw_auth_rule` VALUES ('120', '116', 'route/del', '路线删除', '', '1', '1', '', '0', '0', '');
INSERT INTO `qw_auth_rule` VALUES ('121', '116', 'route/check_attr', '路线检测', '', '1', '1', '', '0', '0', '');
INSERT INTO `qw_auth_rule` VALUES ('122', '116', 'route/update', '路线update', '', '1', '1', '', '0', '0', '');
INSERT INTO `qw_auth_rule` VALUES ('123', '116', 'route/getList', '路线添加信息检索', '', '1', '1', '', '0', '0', '');
INSERT INTO `qw_auth_rule` VALUES ('124', '0', '', '景点管理', '', '1', '1', '', '1', '108', '');
INSERT INTO `qw_auth_rule` VALUES ('125', '124', 'Shopattractions/index', '景点', '', '1', '1', '', '1', '0', '');
INSERT INTO `qw_auth_rule` VALUES ('126', '124', 'Shopattractions/del', '景点删除', '', '1', '1', '', '0', '0', '');
INSERT INTO `qw_auth_rule` VALUES ('127', '124', 'Shopattractions/edit', '景点编辑', '', '1', '1', '', '0', '0', '');
INSERT INTO `qw_auth_rule` VALUES ('128', '124', 'Shopattractions/add', '景点新增', '', '1', '1', '', '0', '0', '');
INSERT INTO `qw_auth_rule` VALUES ('129', '124', 'Shopattractions/update_my', '景点更新(商家)', '', '1', '1', '', '0', '0', '');
INSERT INTO `qw_auth_rule` VALUES ('130', '124', 'Shopattractions/update', '平台景点更新', '', '1', '1', '', '0', '0', '');
INSERT INTO `qw_auth_rule` VALUES ('131', '124', 'Shopattractions/replay', '景点评论', '', '1', '1', '', '0', '0', '');
INSERT INTO `qw_auth_rule` VALUES ('132', '124', 'Shopattractions/replay_del', '景点评论删除', '', '1', '1', '', '0', '0', '');
INSERT INTO `qw_auth_rule` VALUES ('133', '124', 'Shopattractions/replay_submit', '景点评论回复', '', '1', '1', '', '0', '0', '');
INSERT INTO `qw_auth_rule` VALUES ('134', '0', '', '商家核销', '', '1', '1', '', '1', '91', '');
INSERT INTO `qw_auth_rule` VALUES ('135', '134', 'shopcheck/index', '核销', '', '1', '1', '', '1', '0', '');
INSERT INTO `qw_auth_rule` VALUES ('136', '134', 'shopcheck/getInfo', '核销信息', '', '1', '1', '', '0', '0', '');
INSERT INTO `qw_auth_rule` VALUES ('137', '134', 'shopcheck/check', '核销do', '', '1', '1', '', '0', '0', '');
INSERT INTO `qw_auth_rule` VALUES ('138', '0', '', '商家管理', '', '1', '1', '', '1', '107', '');
INSERT INTO `qw_auth_rule` VALUES ('139', '138', 'shop/index', '商家', '', '1', '1', '', '1', '0', '');
INSERT INTO `qw_auth_rule` VALUES ('140', '138', 'shop/add', '商家新增', '', '1', '1', '', '0', '0', '');
INSERT INTO `qw_auth_rule` VALUES ('141', '138', 'shop/edit', '商家编辑', '', '1', '1', '', '0', '0', '');
INSERT INTO `qw_auth_rule` VALUES ('142', '138', 'shop/del', '商家删除', '', '1', '1', '', '0', '0', '');
INSERT INTO `qw_auth_rule` VALUES ('143', '138', 'shop/update_add', '商家update_add', '', '1', '1', '', '0', '0', '');
INSERT INTO `qw_auth_rule` VALUES ('144', '138', 'shop/update', '商家update', '', '1', '1', '', '0', '0', '');
INSERT INTO `qw_auth_rule` VALUES ('145', '0', '', '节日管理', '', '1', '1', '', '1', '109', '');
INSERT INTO `qw_auth_rule` VALUES ('146', '145', 'shopholiday/index', '节日', '', '1', '1', '', '1', '0', '');
INSERT INTO `qw_auth_rule` VALUES ('147', '145', 'shopholiday/add', '节日新增', '', '1', '1', '', '0', '0', '');
INSERT INTO `qw_auth_rule` VALUES ('148', '145', 'shopholiday/edit', '节日编辑', '', '1', '1', '', '0', '0', '');
INSERT INTO `qw_auth_rule` VALUES ('149', '145', 'shopholiday/del', '节日删除', '', '1', '1', '', '0', '0', '');
INSERT INTO `qw_auth_rule` VALUES ('150', '145', 'shopholiday/update_add', '节日新增update(商家)', '', '1', '1', '', '0', '0', '');
INSERT INTO `qw_auth_rule` VALUES ('151', '145', 'shopholiday/update_my', '节日编辑update（商家）', '', '1', '1', '', '0', '0', '');
INSERT INTO `qw_auth_rule` VALUES ('152', '145', 'shopholiday/update', '节日编辑update(平台)', '', '1', '1', '', '0', '0', '');
INSERT INTO `qw_auth_rule` VALUES ('153', '145', 'shopholiday/replay', '节日评论', '', '1', '1', '', '0', '0', '');
INSERT INTO `qw_auth_rule` VALUES ('154', '145', 'shopholiday/replay_del', '节日评论删除', '', '1', '1', '', '0', '0', '');
INSERT INTO `qw_auth_rule` VALUES ('155', '145', 'shopholiday/replay_submit', '节日评论回复', '', '1', '1', '', '0', '0', '');
INSERT INTO `qw_auth_rule` VALUES ('156', '145', 'shopholiday/getList', '节日获取路线', '', '1', '1', '', '0', '0', '');
INSERT INTO `qw_auth_rule` VALUES ('157', '145', 'shopholiday/playUser', '节日报名用户', '', '1', '1', '', '0', '0', '');
INSERT INTO `qw_auth_rule` VALUES ('158', '48', 'shopPass/index', '修改密码', '', '1', '1', '', '1', '0', '');
INSERT INTO `qw_auth_rule` VALUES ('159', '48', 'shopPass/update', '修改密码update', '', '1', '1', '', '0', '0', '');
INSERT INTO `qw_auth_rule` VALUES ('160', '0', 'shoptotal/index', '商家统计', '', '1', '1', '', '1', '102', '');
INSERT INTO `qw_auth_rule` VALUES ('161', '124', 'category/index', '景点分类', '', '1', '1', '', '1', '0', '');
INSERT INTO `qw_auth_rule` VALUES ('162', '124', 'category/add', '景点分类新增', '', '1', '1', '', '0', '0', '');
INSERT INTO `qw_auth_rule` VALUES ('163', '124', 'category/edit', '景点分类编辑', '', '1', '1', '', '0', '0', '');
INSERT INTO `qw_auth_rule` VALUES ('164', '124', 'category/del', '分类删除', '', '1', '1', '', '0', '0', '');
INSERT INTO `qw_auth_rule` VALUES ('165', '124', 'category/update', '分类update', '', '1', '1', '', '0', '0', '');
INSERT INTO `qw_auth_rule` VALUES ('166', '116', 'categoryRoute/index', '路线分类', '', '1', '1', '', '1', '0', '');
INSERT INTO `qw_auth_rule` VALUES ('167', '116', 'categoryRoute/add', '路线分类新增', '', '1', '1', '', '0', '0', '');
INSERT INTO `qw_auth_rule` VALUES ('168', '116', 'categoryRoute/edit', '路线分类编辑', '', '1', '1', '', '0', '0', '');
INSERT INTO `qw_auth_rule` VALUES ('169', '116', 'categoryRoute/del', '分类删除', '', '1', '1', '', '0', '0', '');
INSERT INTO `qw_auth_rule` VALUES ('170', '116', 'categoryRoute/update', '分类update', '', '1', '1', '', '0', '0', '');
INSERT INTO `qw_auth_rule` VALUES ('171', '0', '', '系统消息', '', '1', '1', '', '1', '106', '');
INSERT INTO `qw_auth_rule` VALUES ('172', '171', 'message/index', '系统消息', '', '1', '1', '', '1', '0', '');
INSERT INTO `qw_auth_rule` VALUES ('173', '171', 'message/add', '新增消息', '', '1', '1', '', '0', '0', '');
INSERT INTO `qw_auth_rule` VALUES ('174', '171', 'message/del', '消息删除', '', '1', '1', '', '0', '0', '');
INSERT INTO `qw_auth_rule` VALUES ('175', '171', 'message/update', '新增消息处理', '', '1', '1', '', '0', '0', '');
INSERT INTO `qw_auth_rule` VALUES ('176', '77', 'Destination/del', '删除目的地', '', '1', '1', '', '0', '0', '');
INSERT INTO `qw_auth_rule` VALUES ('177', '68', 'Flash/del', '首页banner删除', '', '1', '1', '', '0', '0', '');
INSERT INTO `qw_auth_rule` VALUES ('178', '124', 'Shopattractions/update_add', '景点新增add', '', '1', '1', '', '0', '0', '');
INSERT INTO `qw_auth_rule` VALUES ('179', '138', 'Shop/hs', '商家核算', '', '1', '1', '', '0', '0', '');

-- ----------------------------
-- Table structure for qw_cid
-- ----------------------------
DROP TABLE IF EXISTS `qw_cid`;
CREATE TABLE `qw_cid` (
  `cid_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `cid_pid` int(10) DEFAULT '0' COMMENT '上级分类',
  `cid_name` varchar(32) NOT NULL COMMENT '分类名称',
  `cid_type` tinyint(1) DEFAULT '0' COMMENT '分类类型(1景点,2目的地，3路线,4节日，5酒店,6餐厅)',
  `cid_sort` int(10) DEFAULT '0' COMMENT '排序(从小到大)',
  `cid_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0禁用,1启用，-1删除',
  PRIMARY KEY (`cid_id`)
) ENGINE=InnoDB AUTO_INCREMENT=73 DEFAULT CHARSET=utf8mb4 COMMENT='景点分类表';

-- ----------------------------
-- Records of qw_cid
-- ----------------------------

-- ----------------------------
-- Table structure for qw_cid_map
-- ----------------------------
DROP TABLE IF EXISTS `qw_cid_map`;
CREATE TABLE `qw_cid_map` (
  `cid_map_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `cid_id` int(10) DEFAULT '0' COMMENT '分类id',
  `join_id` int(10) DEFAULT '0' COMMENT '关联id(如景点,目的地等)',
  `cid_map_sort` int(11) DEFAULT '0' COMMENT '排序',
  `cid_map_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1景点,2目的地，3路线,4节日，5酒店,6餐厅',
  PRIMARY KEY (`cid_map_id`)
) ENGINE=InnoDB AUTO_INCREMENT=318 DEFAULT CHARSET=utf8mb4 COMMENT='分类引用表';

-- ----------------------------
-- Records of qw_cid_map
-- ----------------------------

-- ----------------------------
-- Table structure for qw_destination
-- ----------------------------
DROP TABLE IF EXISTS `qw_destination`;
CREATE TABLE `qw_destination` (
  `destination_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `destination_name` varchar(32) NOT NULL COMMENT '名称',
  `destination_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0禁用,1启用,-1删除',
  `destination_sort` int(11) DEFAULT '0' COMMENT '目的地排序，小的在前',
  `destination_created_at` int(11) DEFAULT '0',
  `destination_updated_at` int(11) DEFAULT '0',
  PRIMARY KEY (`destination_id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COMMENT='目的地表';

-- ----------------------------
-- Records of qw_destination
-- ----------------------------

-- ----------------------------
-- Table structure for qw_destination_join
-- ----------------------------
DROP TABLE IF EXISTS `qw_destination_join`;
CREATE TABLE `qw_destination_join` (
  `destination_join_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `destination_id` int(10) DEFAULT '0' COMMENT '目的地id',
  `join_id` int(10) DEFAULT '0' COMMENT '关联id(如景点,目的地等)',
  `destination_join_sort` int(11) DEFAULT '0' COMMENT '排序',
  `destination_join_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1景点,2目的地，3路线,4节日，5酒店,6餐厅',
  PRIMARY KEY (`destination_join_id`)
) ENGINE=InnoDB AUTO_INCREMENT=93 DEFAULT CHARSET=utf8mb4 COMMENT='目的地相关信息表';

-- ----------------------------
-- Records of qw_destination_join
-- ----------------------------

-- ----------------------------
-- Table structure for qw_devlog
-- ----------------------------
DROP TABLE IF EXISTS `qw_devlog`;
CREATE TABLE `qw_devlog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `v` varchar(225) NOT NULL COMMENT '版本号',
  `y` int(4) NOT NULL COMMENT '年分',
  `t` int(10) NOT NULL COMMENT '发布日期',
  `log` text NOT NULL COMMENT '更新日志',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COMMENT='版本表';

-- ----------------------------
-- Records of qw_devlog
-- ----------------------------
INSERT INTO `qw_devlog` VALUES ('1', '1.0.0', '2016', '1440259200', 'ADMIN第一个版本发布。');
INSERT INTO `qw_devlog` VALUES ('2', '1.0.1', '2016', '1440259200', '修改cookie过于简单的安全风险。');

-- ----------------------------
-- Table structure for qw_failed_jobs
-- ----------------------------
DROP TABLE IF EXISTS `qw_failed_jobs`;
CREATE TABLE `qw_failed_jobs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `connection` text COLLATE utf8_unicode_ci NOT NULL,
  `queue` text COLLATE utf8_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of qw_failed_jobs
-- ----------------------------

-- ----------------------------
-- Table structure for qw_fav
-- ----------------------------
DROP TABLE IF EXISTS `qw_fav`;
CREATE TABLE `qw_fav` (
  `fav_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `join_id` int(10) DEFAULT '0' COMMENT '关联id(如景点,目的地等)',
  `fav_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1景点,2目的地，3路线,4节日，5酒店,6餐厅',
  `user_id` int(10) DEFAULT '0' COMMENT '创建者',
  `fav_created_at` int(11) DEFAULT '0',
  `fav_updated_at` int(11) DEFAULT '0',
  PRIMARY KEY (`fav_id`)
) ENGINE=InnoDB AUTO_INCREMENT=95 DEFAULT CHARSET=utf8mb4 COMMENT='收藏表';

-- ----------------------------
-- Records of qw_fav
-- ----------------------------

-- ----------------------------
-- Table structure for qw_hall
-- ----------------------------
DROP TABLE IF EXISTS `qw_hall`;
CREATE TABLE `qw_hall` (
  `hall_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `hall_name` varchar(32) NOT NULL COMMENT '名称',
  `hall_address` varchar(255) NOT NULL COMMENT '地址',
  `hall_phone` varchar(32) NOT NULL COMMENT '电话',
  `hall_price` decimal(10,2) DEFAULT '0.00' COMMENT '价格',
  `hall_intro` text NOT NULL COMMENT '介绍',
  `hall_score` decimal(10,2) DEFAULT '0.00' COMMENT '评分',
  `hall_evaluation` int(10) DEFAULT '0' COMMENT '评价',
  `hall_lon` varchar(64) DEFAULT '' COMMENT '经度',
  `hall_lat` varchar(64) DEFAULT '' COMMENT '纬度',
  `hall_geohash` varchar(255) DEFAULT '' COMMENT '通过经纬度换算得到的字符串索引',
  `hall_open_time` varchar(255) DEFAULT NULL COMMENT '餐饮开放时间',
  `hall_start_at` int(11) DEFAULT '0' COMMENT '开放时间',
  `hall_end_at` int(11) DEFAULT '0' COMMENT '结束时间',
  `hall_sort` int(10) DEFAULT '0' COMMENT '排序(从小到大)',
  `hall_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0禁用,1启用,-1删除',
  `hall_created_at` int(11) DEFAULT '0',
  `hall_updated_at` int(11) DEFAULT '0',
  `hall_score_num` int(10) DEFAULT '0' COMMENT '评价数(目的地详情页需要用)',
  PRIMARY KEY (`hall_id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COMMENT='餐厅表';

-- ----------------------------
-- Records of qw_hall
-- ----------------------------
-- ----------------------------
-- Table structure for qw_holiday
-- ----------------------------
DROP TABLE IF EXISTS `qw_holiday`;
CREATE TABLE `qw_holiday` (
  `holiday_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `shop_id` int(11) NOT NULL DEFAULT '0' COMMENT '商家id',
  `holiday_name` varchar(32) NOT NULL COMMENT '名称',
  `holiday_address` varchar(255) NOT NULL COMMENT '地址',
  `holiday_phone` varchar(32) NOT NULL COMMENT '电话',
  `holiday_price` decimal(10,2) DEFAULT '0.00' COMMENT '价格',
  `holiday_intro` text NOT NULL COMMENT '介绍',
  `holiday_score` decimal(10,2) DEFAULT '0.00' COMMENT '评分',
  `holiday_evaluation` int(10) DEFAULT '0' COMMENT '评价',
  `holiday_lon` varchar(64) DEFAULT '' COMMENT '经度',
  `holiday_lat` varchar(64) DEFAULT '' COMMENT '纬度',
  `holiday_geohash` varchar(255) DEFAULT '' COMMENT '通过经纬度换算得到的字符串索引',
  `holiday_start_at` int(11) DEFAULT '0' COMMENT '开放时间',
  `holiday_end_at` int(11) DEFAULT '0' COMMENT '结束时间',
  `holiday_open_time` varchar(255) DEFAULT NULL COMMENT '开放时间',
  `holiday_sort` int(10) DEFAULT '0' COMMENT '排序(从小到大)',
  `holiday_is_refund` tinyint(1) DEFAULT '1' COMMENT '是否可退货,0否,1是',
  `holiday_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0禁用,1启用，-1删除',
  `holiday_created_at` int(11) DEFAULT '0',
  `holiday_updated_at` int(11) DEFAULT '0',
  `holiday_suggest` varchar(64) DEFAULT '' COMMENT '建议游玩',
  `holiday_sales_num` int(11) DEFAULT '0' COMMENT '订单数',
  `holiday_score_num` int(11) DEFAULT '0' COMMENT '景点评论数',
  `holiday_sales_total` int(11) DEFAULT '0' COMMENT '报名数，件数',
  PRIMARY KEY (`holiday_id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COMMENT='节日表';

-- ----------------------------
-- Records of qw_holiday
-- ----------------------------
-- ----------------------------
-- Table structure for qw_holiday_join
-- ----------------------------
DROP TABLE IF EXISTS `qw_holiday_join`;
CREATE TABLE `qw_holiday_join` (
  `holiday_join_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `holiday_id` int(10) DEFAULT '0' COMMENT '节日id',
  `route_id` int(10) DEFAULT '0' COMMENT '关联线路id',
  PRIMARY KEY (`holiday_join_id`)
) ENGINE=InnoDB AUTO_INCREMENT=59 DEFAULT CHARSET=utf8mb4 COMMENT='节日相关信息表';

-- ----------------------------
-- Records of qw_holiday_join
-- ----------------------------

-- ----------------------------
-- Table structure for qw_home_page
-- ----------------------------
DROP TABLE IF EXISTS `qw_home_page`;
CREATE TABLE `qw_home_page` (
  `home_page_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '页面id',
  `home_page_name` varchar(50) NOT NULL COMMENT '页面名称',
  `home_page_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '首页框框状态，1开启，0关闭',
  `home_page_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '页面类型，1首页广告 2首页热门线路 3首页热门目的地 4首页景点 5首页节日 6首页推荐周边 7景点分类',
  `home_page_sort` int(11) NOT NULL DEFAULT '1' COMMENT '排序，小在前',
  PRIMARY KEY (`home_page_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COMMENT='首页控制开关';

-- ----------------------------
-- Records of qw_home_page
-- ----------------------------
INSERT INTO `qw_home_page` VALUES ('1', '广告', '1', '1', '0');
INSERT INTO `qw_home_page` VALUES ('2', '路线', '1', '2', '4');
INSERT INTO `qw_home_page` VALUES ('3', '目的地', '1', '3', '5');
INSERT INTO `qw_home_page` VALUES ('4', '景点', '1', '4', '2');
INSERT INTO `qw_home_page` VALUES ('5', '节日', '1', '5', '3');
INSERT INTO `qw_home_page` VALUES ('6', '推荐周边', '1', '6', '6');
INSERT INTO `qw_home_page` VALUES ('7', '景点分类', '1', '7', '1');

-- ----------------------------
-- Table structure for qw_home_page_value
-- ----------------------------
DROP TABLE IF EXISTS `qw_home_page_value`;
CREATE TABLE `qw_home_page_value` (
  `home_page_value_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '记录id',
  `home_page_id` int(11) NOT NULL,
  `value_id` int(11) NOT NULL COMMENT '首页分类对应的值id',
  `sort` int(11) NOT NULL DEFAULT '1' COMMENT '排序，小的在前面',
  PRIMARY KEY (`home_page_value_id`)
) ENGINE=InnoDB AUTO_INCREMENT=518 DEFAULT CHARSET=utf8 COMMENT='首页控制对应的值';

-- ----------------------------
-- Records of qw_home_page_value
-- ----------------------------

-- ----------------------------
-- Table structure for qw_hotel
-- ----------------------------
DROP TABLE IF EXISTS `qw_hotel`;
CREATE TABLE `qw_hotel` (
  `hotel_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `hotel_name` varchar(32) NOT NULL COMMENT '名称',
  `hotel_address` varchar(255) NOT NULL COMMENT '地址',
  `hotel_phone` varchar(32) NOT NULL COMMENT '电话',
  `hotel_price` decimal(10,2) DEFAULT '0.00' COMMENT '价格',
  `hotel_intro` text NOT NULL COMMENT '介绍',
  `hotel_score` decimal(10,2) DEFAULT '0.00' COMMENT '评分',
  `hotel_evaluation` int(10) DEFAULT '0' COMMENT '评价',
  `hotel_lon` varchar(64) DEFAULT '' COMMENT '经度',
  `hotel_lat` varchar(64) DEFAULT '' COMMENT '纬度',
  `hotel_geohash` varchar(255) DEFAULT '' COMMENT '通过经纬度换算得到的字符串索引',
  `hotel_open_time` varchar(255) DEFAULT NULL COMMENT '酒店开放时间',
  `hotel_start_at` int(11) DEFAULT '0' COMMENT '开放时间',
  `hotel_end_at` int(11) DEFAULT '0' COMMENT '结束时间',
  `hotel_sort` int(10) DEFAULT '0' COMMENT '排序(从小到大)',
  `hotel_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0禁用,1启用，-1删除',
  `hotel_created_at` int(11) DEFAULT '0',
  `hotel_updated_at` int(11) DEFAULT '0',
  `hotel_score_num` int(10) DEFAULT '0' COMMENT '评价数(目的地详情页需要用)',
  PRIMARY KEY (`hotel_id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COMMENT='酒店表';

-- ----------------------------
-- Records of qw_hotel
-- ----------------------------
-- ----------------------------
-- Table structure for qw_img
-- ----------------------------
DROP TABLE IF EXISTS `qw_img`;
CREATE TABLE `qw_img` (
  `img_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `join_id` int(10) DEFAULT '0' COMMENT '关联id(如景点,目的地等)',
  `img_sort` int(11) DEFAULT '0' COMMENT '排序',
  `img_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1景点,2目的地，3路线,4节日，5酒店,6餐厅, 8评价,9建议反馈',
  `img_url` varchar(255) NOT NULL,
  `img_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0禁用,1启用',
  `img_created_at` int(11) DEFAULT '0',
  `img_updated_at` int(11) DEFAULT '0',
  PRIMARY KEY (`img_id`)
) ENGINE=InnoDB AUTO_INCREMENT=370 DEFAULT CHARSET=utf8mb4 COMMENT='业务图片表';

-- ----------------------------
-- Records of qw_img
-- ----------------------------

-- ----------------------------
-- Table structure for qw_log
-- ----------------------------
DROP TABLE IF EXISTS `qw_log`;
CREATE TABLE `qw_log` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `log_type` tinyint(11) DEFAULT '0' COMMENT '1登录，2分享，3首页浏览, 4收藏',
  `log_time` int(11) DEFAULT '0' COMMENT '记录时间戳',
  `user_id` int(11) DEFAULT '0' COMMENT '操作人',
  `log_ip` varchar(50) DEFAULT NULL COMMENT '记录ip',
  `join_id` int(11) DEFAULT '0' COMMENT '分享id，',
  `log_join_type` int(11) DEFAULT '0' COMMENT '1景点,2目的地，3路线,4节日，5酒店,6餐厅，7图片',
  PRIMARY KEY (`log_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9569 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of qw_log
-- ----------------------------

-- ----------------------------
-- Table structure for qw_message
-- ----------------------------
DROP TABLE IF EXISTS `qw_message`;
CREATE TABLE `qw_message` (
  `message_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT '0' COMMENT '接收者id',
  `message_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1全部用户,2单个用户',
  `message_status` tinyint(1) DEFAULT '1' COMMENT '0删除，1有效',
  `message_read` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0未读1已读',
  `message_title` varchar(128) DEFAULT '' COMMENT '消息标题',
  `message_comment` text NOT NULL COMMENT '内容 htmlspecialchars',
  `created_user_id` int(10) DEFAULT '0' COMMENT '创建者',
  `message_created_at` int(11) DEFAULT '0',
  `message_updated_at` int(11) DEFAULT '0',
  PRIMARY KEY (`message_id`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COMMENT='消息表';

-- ----------------------------
-- Records of qw_message
-- ----------------------------

-- ----------------------------
-- Table structure for qw_order
-- ----------------------------
DROP TABLE IF EXISTS `qw_order`;
CREATE TABLE `qw_order` (
  `order_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `shop_id` int(11) NOT NULL DEFAULT '0' COMMENT '商家id',
  `order_sn` varchar(50) NOT NULL COMMENT '订单号',
  `join_id` int(10) DEFAULT '0' COMMENT '商品关联id(如景点,目的地等)',
  `order_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '订单类型1景点,2目的地，3路线,4节日，5酒店,6餐厅,7图片',
  `order_num` int(10) DEFAULT '0' COMMENT '商品数量',
  `order_price` decimal(10,2) DEFAULT '0.00' COMMENT '商品单价',
  `order_amount` decimal(10,2) DEFAULT '0.00' COMMENT '商品总价',
  `order_pay_amount` decimal(10,2) DEFAULT '0.00' COMMENT '订单实付款',
  `order_refund_amount` decimal(10,2) DEFAULT '0.00' COMMENT '退款金额',
  `order_reward_amount` decimal(2,0) DEFAULT '0' COMMENT '奖励的红包金额',
  `order_is_score` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否评价,0未,1已',
  `order_status` tinyint(1) NOT NULL DEFAULT '10' COMMENT '订单状态(10未付款,20已支付，30已核销，40已评价，0已取消',
  `order_cancel_type` tinyint(1) DEFAULT '0' COMMENT '取消方式(1未付款手动取消,2未付款自动取消,3已付款手动取消[退款中],4已付款手动取消[退款完成])',
  `user_id` int(10) NOT NULL DEFAULT '0' COMMENT '创建者',
  `order_pay_at` int(11) DEFAULT '0' COMMENT '支付时间',
  `order_refund_at` int(11) DEFAULT '0' COMMENT '退款时间',
  `order_cancel_at` int(11) DEFAULT '0' COMMENT '取消时间',
  `order_created_at` int(11) DEFAULT '0',
  `order_updated_at` int(11) DEFAULT '0',
  `order_check_at` int(11) DEFAULT '0' COMMENT '核算时间',
  `prepay_id` varchar(64) DEFAULT '' COMMENT '微信预支付订单(用于创建微信订单后未支付用)',
  `transaction_id` varchar(64) DEFAULT '' COMMENT '第三方订单号(如微信支付成功后获得)',
  `refund_id` varchar(64) DEFAULT '' COMMENT '微信退款订单号(退款成功时记录)',
  `payment_no` varchar(64) DEFAULT '' COMMENT '微信企业付款成功后订单号(红包奖励完成记录)',
  `is_refund` tinyint(1) DEFAULT '1' COMMENT '是否可退款,0否,1是',
  `original_id` varchar(32) DEFAULT '' COMMENT '主订单(用于线路购买下单时候,相关订单的一个绑定,便于查询统计等)',
  PRIMARY KEY (`order_id`)
) ENGINE=InnoDB AUTO_INCREMENT=579 DEFAULT CHARSET=utf8mb4 COMMENT='订单表';

-- ----------------------------
-- Records of qw_order
-- ----------------------------
-- ----------------------------
-- Table structure for qw_order_code
-- ----------------------------
DROP TABLE IF EXISTS `qw_order_code`;
CREATE TABLE `qw_order_code` (
  `order_code_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `shop_id` int(11) DEFAULT '0' COMMENT '商家id',
  `order_id` int(11) NOT NULL DEFAULT '0' COMMENT '订单id',
  `code` varchar(64) NOT NULL COMMENT '兑换码',
  `is_exchange` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否兑换核销,0未,1已',
  `exchange_user_id` int(10) DEFAULT '0' COMMENT '核销人员(管理员)',
  `exchange_at` int(10) NOT NULL DEFAULT '0' COMMENT '核销时间',
  `created_at` int(10) NOT NULL DEFAULT '0' COMMENT '添加时间',
  PRIMARY KEY (`order_code_id`)
) ENGINE=InnoDB AUTO_INCREMENT=141 DEFAULT CHARSET=utf8mb4 COMMENT='订单表';

-- ----------------------------
-- Records of qw_order_code
-- ----------------------------
-- ----------------------------
-- Table structure for qw_order_sms
-- ----------------------------
DROP TABLE IF EXISTS `qw_order_sms`;
CREATE TABLE `qw_order_sms` (
  `order_sms_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL DEFAULT '0' COMMENT '订单id',
  `created_at` int(10) NOT NULL DEFAULT '0' COMMENT '发送时间',
  PRIMARY KEY (`order_sms_id`)
) ENGINE=InnoDB AUTO_INCREMENT=62 DEFAULT CHARSET=utf8mb4 COMMENT='订单发送消息记录表(用于节日订单提醒)';

-- ----------------------------
-- Records of qw_order_sms
-- ----------------------------

-- ----------------------------
-- Table structure for qw_page_view
-- ----------------------------
DROP TABLE IF EXISTS `qw_page_view`;
CREATE TABLE `qw_page_view` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `add_time` datetime NOT NULL,
  `add_time_int` int(11) NOT NULL,
  `page_name` varchar(50) DEFAULT NULL,
  `page_value` varchar(50) DEFAULT NULL,
  `ip` varchar(50) DEFAULT NULL,
  `agent` text,
  `url` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`log_id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COMMENT='用户行为表';

-- ----------------------------
-- Records of qw_page_view
-- ----------------------------
-- ----------------------------
-- Table structure for qw_red_status
-- ----------------------------
DROP TABLE IF EXISTS `qw_red_status`;
CREATE TABLE `qw_red_status` (
  `red_id` int(11) NOT NULL AUTO_INCREMENT,
  `red_status` tinyint(1) DEFAULT '1' COMMENT '1开启，0关闭',
  `red_start_num` int(11) DEFAULT '0' COMMENT '红包开始金额',
  `red_end_num` int(11) DEFAULT '0' COMMENT '红包结束金额',
  PRIMARY KEY (`red_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COMMENT='红包开关表';

-- ----------------------------
-- Records of qw_red_status
-- ----------------------------
INSERT INTO `qw_red_status` VALUES ('1', '1', '1', '2');

-- ----------------------------
-- Table structure for qw_route
-- ----------------------------
DROP TABLE IF EXISTS `qw_route`;
CREATE TABLE `qw_route` (
  `route_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `route_name` varchar(32) NOT NULL COMMENT '名称',
  `route_day_num` tinyint(1) NOT NULL DEFAULT '1' COMMENT '天数',
  `user_id` int(10) DEFAULT '0' COMMENT '线路创建者0是官方路线，不允许修改',
  `route_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0禁用,1启用，-1删除',
  `route_created_at` int(11) DEFAULT '0',
  `route_updated_at` int(11) DEFAULT '0',
  `route_intro` text COMMENT '简介html_specialchars',
  `route_use_num` int(10) DEFAULT '0' COMMENT '使用次数(目的地详情页需要用)',
  `route_lon` varchar(64) DEFAULT NULL COMMENT '经度',
  `route_lat` varchar(64) DEFAULT NULL COMMENT '维度',
  `route_geohash` varchar(255) DEFAULT NULL COMMENT '通过经纬度换算得到的字符串索引',
  `route_address` varchar(255) DEFAULT NULL COMMENT '详细地址',
  PRIMARY KEY (`route_id`)
) ENGINE=InnoDB AUTO_INCREMENT=124 DEFAULT CHARSET=utf8mb4 COMMENT='路线表';

-- ----------------------------
-- Records of qw_route
-- ----------------------------
-- ----------------------------
-- Table structure for qw_route_day
-- ----------------------------
DROP TABLE IF EXISTS `qw_route_day`;
CREATE TABLE `qw_route_day` (
  `route_day_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `route_id` int(10) DEFAULT '0' COMMENT '目的地id',
  `route_day_intro` varchar(255) NOT NULL COMMENT '日程介绍',
  `route_day_sort` int(11) DEFAULT '0' COMMENT '排序，从小到大，小的在前面',
  PRIMARY KEY (`route_day_id`)
) ENGINE=InnoDB AUTO_INCREMENT=271 DEFAULT CHARSET=utf8mb4 COMMENT='路线日程表';

-- ----------------------------
-- Records of qw_route_day
-- ----------------------------
-- ----------------------------
-- Table structure for qw_route_day_join
-- ----------------------------
DROP TABLE IF EXISTS `qw_route_day_join`;
CREATE TABLE `qw_route_day_join` (
  `route_day_join_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `route_day_id` int(10) DEFAULT '0' COMMENT '线路日程id',
  `join_id` int(10) DEFAULT '0' COMMENT '关联id(如景点,目的地等)',
  `route_day_join_sort` int(11) DEFAULT '0' COMMENT '排序',
  `route_day_join_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1景点,2目的地，3路线,4节日，5酒店,6餐厅,7图片',
  `route_id` int(10) DEFAULT '0' COMMENT '线路id(方便取线路下的第一张图片)',
  PRIMARY KEY (`route_day_join_id`)
) ENGINE=InnoDB AUTO_INCREMENT=531 DEFAULT CHARSET=utf8mb4 COMMENT='路线日程相关数据表';

-- ----------------------------
-- Records of qw_route_day_join
-- ----------------------------
-- ----------------------------
-- Table structure for qw_score
-- ----------------------------
DROP TABLE IF EXISTS `qw_score`;
CREATE TABLE `qw_score` (
  `score_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) DEFAULT '0' COMMENT '评价创建者',
  `join_id` int(10) DEFAULT '0' COMMENT '关联id(如景点,目的地等)',
  `order_id` int(10) DEFAULT '0' COMMENT '关联的订单id',
  `score` decimal(10,2) DEFAULT '0.00' COMMENT '评分',
  `score_comment` varchar(255) NOT NULL COMMENT '内容',
  `score_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1景点,2目的地，3路线,4节日，5酒店,6餐厅,7图片,8评论',
  `score_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0禁用,1启用，-1删除',
  `score_created_at` int(11) DEFAULT '0',
  `score_updated_at` int(11) DEFAULT '0',
  `score_from_id` varchar(255) DEFAULT NULL COMMENT '用户发表评论是，from表单形式的fromid，具体看小程序模板推送',
  `score_replay_status` tinyint(1) DEFAULT '0' COMMENT '0未回复，1已经回复',
  `score_replay_content` text COMMENT '回复评论的内容',
  PRIMARY KEY (`score_id`)
) ENGINE=InnoDB AUTO_INCREMENT=217 DEFAULT CHARSET=utf8mb4 COMMENT='评价表';

-- ----------------------------
-- Records of qw_score
-- ----------------------------
-- ----------------------------
-- Table structure for qw_setting
-- ----------------------------
DROP TABLE IF EXISTS `qw_setting`;
CREATE TABLE `qw_setting` (
  `k` varchar(100) NOT NULL COMMENT '变量',
  `v` varchar(255) NOT NULL COMMENT '值',
  `type` tinyint(1) NOT NULL COMMENT '0系统，1自定义',
  `name` varchar(255) NOT NULL COMMENT '说明',
  `status` tinyint(1) DEFAULT '0' COMMENT '0普通，1图片，2html',
  PRIMARY KEY (`k`),
  KEY `k` (`k`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COMMENT='后台设置表';

-- ----------------------------
-- Records of qw_setting
-- ----------------------------
INSERT INTO `qw_setting` VALUES ('sitename', '趣玩666', '0', '', '0');
INSERT INTO `qw_setting` VALUES ('title', '趣玩后台', '0', '', '0');
INSERT INTO `qw_setting` VALUES ('keywords', '关键词', '0', '', '0');
INSERT INTO `qw_setting` VALUES ('description', '网站描述', '0', '', '0');
INSERT INTO `qw_setting` VALUES ('footer', '2016©趣玩', '0', '', '0');
INSERT INTO `qw_setting` VALUES ('top_banner', '/Public/attached/201609/201609141613191296.png', '1', '顶部banner图片', '1');

-- ----------------------------
-- Table structure for qw_shop
-- ----------------------------
DROP TABLE IF EXISTS `qw_shop`;
CREATE TABLE `qw_shop` (
  `shop_id` int(11) NOT NULL AUTO_INCREMENT,
  `shop_title` varchar(255) DEFAULT NULL COMMENT '店铺名称',
  `shop_name` varchar(255) DEFAULT NULL COMMENT '商家联系人',
  `shop_mobile` varchar(50) NOT NULL COMMENT '商家登录号',
  `shop_phone` varchar(50) DEFAULT NULL COMMENT '商家联系号码',
  `shop_address` varchar(255) DEFAULT NULL COMMENT '商家地址',
  `shop_lon` varchar(50) DEFAULT NULL COMMENT '商家经度',
  `shop_lat` varchar(50) DEFAULT NULL COMMENT '商家纬度',
  `shop_geohash` varchar(50) DEFAULT NULL COMMENT '地址hash',
  `shop_lastmonth_money` decimal(10,2) DEFAULT '0.00' COMMENT '上月结余',
  `shop_money` decimal(10,2) DEFAULT '0.00' COMMENT '本月结余',
  `shop_status` tinyint(1) DEFAULT '-1' COMMENT '初始为-1，0的时候重置了密码，1的时候完善信息,-2删除的商家',
  `shop_desc` text COMMENT '商家描述',
  `shop_ver` int(11) NOT NULL DEFAULT '0' COMMENT '版本控制',
  `shop_crontab_time` date DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`shop_id`),
  UNIQUE KEY `shop_mobile` (`shop_mobile`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COMMENT='商家信息';

-- ----------------------------
-- Records of qw_shop
-- ----------------------------
-- ----------------------------
-- Table structure for qw_suggest
-- ----------------------------
DROP TABLE IF EXISTS `qw_suggest`;
CREATE TABLE `qw_suggest` (
  `suggest_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) DEFAULT '0' COMMENT '评价创建者',
  `suggest_comment` varchar(255) NOT NULL COMMENT '内容',
  `suggest_phone` varchar(64) DEFAULT '' COMMENT '联系方式',
  `suggest_replay_status` tinyint(1) DEFAULT '0' COMMENT '0未回复，1已经回复',
  `suggest_created_at` int(11) DEFAULT '0',
  `suggest_updated_at` int(11) DEFAULT '0',
  PRIMARY KEY (`suggest_id`)
) ENGINE=InnoDB AUTO_INCREMENT=104 DEFAULT CHARSET=utf8mb4 COMMENT='建议反馈表';

-- ----------------------------
-- Records of qw_suggest
-- ----------------------------

-- ----------------------------
-- Table structure for qw_user
-- ----------------------------
DROP TABLE IF EXISTS `qw_user`;
CREATE TABLE `qw_user` (
  `user_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_nickname` varchar(45) NOT NULL COMMENT '昵称',
  `user_sex` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0女,1男,2保密',
  `user_avatar` varchar(255) NOT NULL COMMENT '头像',
  `user_mobile` varchar(32) NOT NULL DEFAULT '' COMMENT '手机号码',
  `user_is_binding` tinyint(1) NOT NULL DEFAULT '0' COMMENT '手机绑定(0未绑定,1已绑定)',
  `openid` varchar(62) NOT NULL DEFAULT '' COMMENT '微信openid',
  `user_lon` varchar(64) DEFAULT '' COMMENT '经度',
  `user_lat` varchar(64) DEFAULT '' COMMENT '纬度',
  `user_geohash` varchar(255) DEFAULT '' COMMENT '通过经纬度换算得到的字符串索引',
  `user_msg_num` int(10) unsigned DEFAULT '0' COMMENT '未读消息数(后台发一条+1,用户读一条-1)',
  `user_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0禁用,1启用',
  `user_created_at` int(11) DEFAULT '0',
  `user_updated_at` int(11) DEFAULT '0',
  `user_money` decimal(10,2) DEFAULT '0.00' COMMENT '用户余额',
  `user_total_money` decimal(10,2) DEFAULT '0.00' COMMENT '累计消费金额',
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=57 DEFAULT CHARSET=utf8mb4 COMMENT='用户表';

-- ----------------------------
-- Records of qw_user
-- ----------------------------