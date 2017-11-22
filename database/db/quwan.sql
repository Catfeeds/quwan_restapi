/*
 Navicat Premium Data Transfer

 Source Server         : 119.29.87.252
 Source Server Type    : MySQL
 Source Server Version : 50636
 Source Host           : 119.29.87.252
 Source Database       : quwan

 Target Server Type    : MySQL
 Target Server Version : 50636
 File Encoding         : utf-8

 Date: 11/19/2017 22:01:19 PM
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
--  Table structure for `qw_admin`
-- ----------------------------
DROP TABLE IF EXISTS `qw_admin`;
CREATE TABLE `qw_admin` (
  `admin_id` int(11) NOT NULL AUTO_INCREMENT,
  `user` varchar(225) NOT NULL,
  `head` varchar(255) NOT NULL COMMENT '头像',
  `sex` tinyint(1) NOT NULL COMMENT '0保密1男，2女',
  `birthday` int(10) NOT NULL COMMENT '生日',
  `phone` varchar(20) NOT NULL COMMENT '电话',
  `qq` varchar(20) NOT NULL COMMENT 'QQ',
  `email` varchar(255) NOT NULL COMMENT '邮箱',
  `password` varchar(32) NOT NULL,
  `t` int(10) unsigned NOT NULL COMMENT '注册时间',
  `identifier` varchar(32) NOT NULL,
  `token` varchar(32) NOT NULL,
  `salt` varchar(10) NOT NULL,
  PRIMARY KEY (`admin_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COMMENT='后台用户表';

-- ----------------------------
--  Records of `qw_admin`
-- ----------------------------
BEGIN;
INSERT INTO `qw_admin` VALUES ('1', 'admin', '2017-11-15_5a0be7db38fd4.png', '1', '1420128000', '13800138000', '331349451', '273719650@qq.com', '66d6a1c8748025462128dc75bf5ae8d1', '1442505600', '140c8b3baa2d9adadd3b61cef5be5309', '11df0e4b1abc97c12aa7f26d7ac80d75', 'AhjKxGA2d9');
COMMIT;

-- ----------------------------
--  Table structure for `qw_admin_log`
-- ----------------------------
DROP TABLE IF EXISTS `qw_admin_log`;
CREATE TABLE `qw_admin_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) DEFAULT '0',
  `name` varchar(100) NOT NULL,
  `t` int(10) NOT NULL,
  `ip` varchar(16) NOT NULL,
  `log` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COMMENT='后台用户日志表';

-- ----------------------------
--  Records of `qw_admin_log`
-- ----------------------------
BEGIN;
INSERT INTO `qw_admin_log` VALUES ('27', null, 'admin', '1510568375', '127.0.0.1', '备份文件删除成功！'), ('26', null, 'admin', '1510568364', '127.0.0.1', '备份文件删除成功！'), ('25', null, 'admin', '1510568349', '127.0.0.1', '备份完成！'), ('24', null, 'admin', '1510568344', '127.0.0.1', '备份完成！'), ('23', null, 'admin', '1510568216', '127.0.0.1', '修改网站配置。'), ('22', null, 'admin', '1510568194', '127.0.0.1', '修改网站配置。'), ('21', null, 'admin', '1510567808', '127.0.0.1', '新增用户组，ID：0，组名：商家组'), ('20', null, 'admin', '1510567760', '127.0.0.1', '登录成功。'), ('28', '0', 'admin', '1510652810', '127.0.0.1', '登录成功。'), ('29', '1', 'admin', '1510729974', '127.0.0.1', '修改个人资料'), ('30', '1', 'admin', '1510732303', '127.0.0.1', '登录成功。'), ('31', '1', 'admin', '1510987481', '42.245.252.16', '登录成功。');
COMMIT;

-- ----------------------------
--  Table structure for `qw_admin_shop`
-- ----------------------------
DROP TABLE IF EXISTS `qw_admin_shop`;
CREATE TABLE `qw_admin_shop` (
  `shop_id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  PRIMARY KEY (`shop_id`,`admin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='管理用户和商家对应表';

-- ----------------------------
--  Table structure for `qw_adv`
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
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin COMMENT='广告表';

-- ----------------------------
--  Records of `qw_adv`
-- ----------------------------
BEGIN;
INSERT INTO `qw_adv` VALUES ('11', '广告标题0', 'http://www.baidu.com', '0', '1', 'http://ww1.sinaimg.cn/large/828dc694gy1flebsus1vsj20d707o75u.jpg', '1', '0', '0', '图文内容0'), ('12', '广告标题1', 'http://www.baidu.com', '1', '1', 'http://ww1.sinaimg.cn/large/828dc694gy1flebsus1vsj20d707o75u.jpg', '1', '0', '0', '图文内容1'), ('13', '广告标题2', 'http://www.baidu.com', '2', '1', 'http://ww1.sinaimg.cn/large/828dc694gy1flebsus1vsj20d707o75u.jpg', '1', '0', '0', '图文内容2'), ('14', '广告标题3', 'http://www.baidu.com', '3', '1', 'http://ww1.sinaimg.cn/large/828dc694gy1flebsus1vsj20d707o75u.jpg', '1', '0', '0', '图文内容3'), ('15', '广告标题4', 'http://www.baidu.com', '4', '1', 'http://ww1.sinaimg.cn/large/828dc694gy1flebsus1vsj20d707o75u.jpg', '1', '0', '0', '图文内容4'), ('16', '广告标题5', 'http://www.baidu.com', '5', '1', 'http://ww1.sinaimg.cn/large/828dc694gy1flebsus1vsj20d707o75u.jpg', '1', '0', '0', '图文内容5'), ('17', '广告标题6', 'http://www.baidu.com', '6', '1', 'http://ww1.sinaimg.cn/large/828dc694gy1flebsus1vsj20d707o75u.jpg', '1', '0', '0', '图文内容6'), ('18', '广告标题7', 'http://www.baidu.com', '7', '1', 'http://ww1.sinaimg.cn/large/828dc694gy1flebsus1vsj20d707o75u.jpg', '1', '0', '0', '图文内容7'), ('19', '广告标题8', 'http://www.baidu.com', '8', '1', 'http://ww1.sinaimg.cn/large/828dc694gy1flebsus1vsj20d707o75u.jpg', '1', '0', '0', '图文内容8'), ('20', '广告标题9', 'http://www.baidu.com', '9', '1', 'http://ww1.sinaimg.cn/large/828dc694gy1flebsus1vsj20d707o75u.jpg', '1', '0', '0', '图文内容9');
COMMIT;

-- ----------------------------
--  Table structure for `qw_attractions`
-- ----------------------------
DROP TABLE IF EXISTS `qw_attractions`;
CREATE TABLE `qw_attractions` (
  `attractions_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `shop_id` int(11) NOT NULL DEFAULT '0' COMMENT '商家id',
  `attractions_name` varchar(32) NOT NULL COMMENT '景点名称',
  `attractions_address` varchar(255) NOT NULL COMMENT '地址',
  `attractions_phone` varchar(32) NOT NULL COMMENT '电话',
  `attractions_price` decimal(10,2) DEFAULT '0.00' COMMENT '价格',
  `attractions_intro` varchar(1000) NOT NULL DEFAULT '' COMMENT '介绍',
  `attractions_score` decimal(10,1) DEFAULT '0.0' COMMENT '评分',
  `attractions_evaluation` decimal(10,2) DEFAULT '0.00' COMMENT '评价',
  `attractions_is_refund` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否可退货,0否,1是',
  `attractions_lon` varchar(64) NOT NULL DEFAULT '' COMMENT '经度',
  `attractions_lat` varchar(64) NOT NULL DEFAULT '' COMMENT '纬度',
  `attractions_geohash` varchar(255) DEFAULT '' COMMENT '通过经纬度换算得到的字符串索引',
  `attractions_start_at` int(11) DEFAULT '0' COMMENT '开放时间',
  `attractions_end_at` int(11) DEFAULT '0' COMMENT '结束时间',
  `attractions_sort` int(10) DEFAULT '0' COMMENT '排序(从小到大)',
  `attractions_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0禁用,1启用',
  `attractions_created_at` int(11) DEFAULT '0',
  `attractions_updated_at` int(11) DEFAULT '0',
  `attractions_suggest` varchar(64) DEFAULT '' COMMENT '建议游玩',
  `attractions_sales_num` int(10) DEFAULT '0' COMMENT '销售数(目的地详情页需要用)',
  PRIMARY KEY (`attractions_id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COMMENT='景点表';

-- ----------------------------
--  Records of `qw_attractions`
-- ----------------------------
BEGIN;
INSERT INTO `qw_attractions` VALUES ('1', '0', '景点名称0', '地址0', '0571-45648970', '250.89', '介绍0', '82.3', '8.90', '1', '30.546566', '100.075546', '', '1510917930', '1510927930', '0', '1', '1510917930', '0', '', '0'), ('2', '0', '景点名称1', '地址1', '0571-45648971', '251.89', '介绍1', '82.3', '8.91', '1', '31.546566', '101.075546', '', '1510917929', '1510927931', '1', '1', '1510917930', '0', '', '0'), ('3', '0', '景点名称2', '地址2', '0571-45648972', '252.89', '介绍2', '82.3', '8.92', '1', '32.546566', '102.075546', '', '1510917928', '1510927932', '2', '1', '1510917930', '0', '', '0'), ('4', '0', '景点名称3', '地址3', '0571-45648973', '253.89', '介绍3', '82.3', '8.93', '1', '33.546566', '103.075546', '', '1510917927', '1510927933', '3', '1', '1510917930', '0', '', '0'), ('5', '0', '景点名称4', '地址4', '0571-45648974', '254.89', '介绍4', '82.3', '8.94', '1', '34.546566', '104.075546', '', '1510917926', '1510927934', '4', '1', '1510917930', '0', '', '0'), ('6', '0', '景点名称5', '地址5', '0571-45648975', '255.89', '介绍5', '82.4', '8.95', '1', '35.546566', '105.075546', '', '1510917925', '1510927935', '5', '1', '1510917930', '0', '', '0'), ('7', '0', '景点名称6', '地址6', '0571-45648976', '256.89', '介绍6', '82.4', '8.96', '1', '36.546566', '106.075546', '', '1510917924', '1510927936', '6', '1', '1510917930', '0', '', '0'), ('8', '0', '景点名称7', '地址7', '0571-45648977', '257.89', '介绍7', '82.4', '8.97', '1', '37.546566', '107.075546', '', '1510917923', '1510927937', '7', '1', '1510917930', '0', '', '0'), ('9', '0', '景点名称8', '地址8', '0571-45648978', '258.89', '介绍8', '82.4', '8.98', '1', '38.546566', '108.075546', '', '1510917922', '1510927938', '8', '1', '1510917930', '0', '', '0'), ('10', '0', '景点名称9', '地址9', '0571-45648979', '259.89', '介绍9', '82.4', '8.99', '1', '39.546566', '109.075546', '', '1510917921', '1510927939', '9', '1', '1510917930', '0', '', '0'), ('11', '0', '景点名称10', '地址10', '0571-456489710', '2510.89', '介绍10', '82.3', '8.91', '1', '310.546566', '1010.075546', '', '1510917920', '1510927940', '10', '1', '1510917930', '0', '', '0'), ('12', '0', '景点名称11', '地址11', '0571-456489711', '2511.89', '介绍11', '82.3', '8.91', '1', '311.546566', '1011.075546', '', '1510917919', '1510927941', '11', '1', '1510917930', '0', '', '0'), ('13', '0', '景点名称12', '地址12', '0571-456489712', '2512.89', '介绍12', '82.3', '8.91', '1', '312.546566', '1012.075546', '', '1510917918', '1510927942', '12', '1', '1510917930', '0', '', '0'), ('14', '0', '景点名称13', '地址13', '0571-456489713', '2513.89', '介绍13', '82.3', '8.91', '1', '313.546566', '1013.075546', '', '1510917917', '1510927943', '13', '1', '1510917930', '0', '', '0'), ('15', '0', '景点名称14', '地址14', '0571-456489714', '2514.89', '介绍14', '82.3', '8.91', '1', '314.546566', '1014.075546', '', '1510917916', '1510927944', '14', '1', '1510917930', '0', '', '0'), ('16', '0', '景点名称15', '地址15', '0571-456489715', '2515.89', '介绍15', '82.3', '8.92', '1', '315.546566', '1015.075546', '', '1510917915', '1510927945', '15', '1', '1510917930', '0', '', '0'), ('17', '0', '景点名称16', '地址16', '0571-456489716', '2516.89', '介绍16', '82.3', '8.92', '1', '316.546566', '1016.075546', '', '1510917914', '1510927946', '16', '1', '1510917930', '0', '', '0'), ('18', '0', '景点名称17', '地址17', '0571-456489717', '2517.89', '介绍17', '82.3', '8.92', '1', '317.546566', '1017.075546', '', '1510917913', '1510927947', '17', '1', '1510917930', '0', '', '0'), ('19', '0', '景点名称18', '地址18', '0571-456489718', '2518.89', '介绍18', '82.3', '8.92', '1', '318.546566', '1018.075546', '', '1510917912', '1510927948', '18', '1', '1510917930', '0', '', '0'), ('20', '0', '景点名称19', '地址19', '0571-456489719', '2519.89', '介绍19', '82.3', '8.92', '1', '319.546566', '1019.075546', '', '1510917911', '1510927949', '19', '1', '1510917930', '0', '', '0');
COMMIT;

-- ----------------------------
--  Table structure for `qw_auth_group`
-- ----------------------------
DROP TABLE IF EXISTS `qw_auth_group`;
CREATE TABLE `qw_auth_group` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `title` char(100) NOT NULL DEFAULT '',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `rules` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COMMENT='用户组表';

-- ----------------------------
--  Records of `qw_auth_group`
-- ----------------------------
BEGIN;
INSERT INTO `qw_auth_group` VALUES ('1', '超级管理员', '1', '0'), ('2', '商家组', '1', '48,49,50,55');
COMMIT;

-- ----------------------------
--  Table structure for `qw_auth_group_access`
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
--  Records of `qw_auth_group_access`
-- ----------------------------
BEGIN;
INSERT INTO `qw_auth_group_access` VALUES ('1', '1');
COMMIT;

-- ----------------------------
--  Table structure for `qw_auth_rule`
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
) ENGINE=MyISAM AUTO_INCREMENT=68 DEFAULT CHARSET=utf8mb4 COMMENT='权限菜单规则表';

-- ----------------------------
--  Records of `qw_auth_rule`
-- ----------------------------
BEGIN;
INSERT INTO `qw_auth_rule` VALUES ('1', '0', 'Index/index', '控制台', 'menu-icon fa fa-tachometer', '1', '1', '', '1', '1', '友情提示：经常查看操作日志，发现异常以便及时追查原因。'), ('2', '0', '', '系统设置', 'menu-icon fa fa-cog', '1', '1', '', '1', '2', ''), ('3', '2', 'Setting/setting', '网站设置', 'menu-icon fa fa-caret-right', '1', '1', '', '1', '3', '这是网站设置的提示。'), ('4', '2', 'Menu/index', '后台菜单', 'menu-icon fa fa-caret-right', '1', '1', '', '1', '4', ''), ('5', '2', 'Menu/add', '新增菜单', 'menu-icon fa fa-caret-right', '1', '1', '', '1', '5', ''), ('6', '4', 'Menu/edit', '编辑菜单', '', '1', '1', '', '0', '6', ''), ('7', '2', 'Menu/update', '保存菜单', 'menu-icon fa fa-caret-right', '1', '1', '', '0', '7', ''), ('8', '2', 'Menu/del', '删除菜单', 'menu-icon fa fa-caret-right', '1', '1', '', '0', '8', ''), ('9', '2', 'Database/backup', '数据库备份', 'menu-icon fa fa-caret-right', '1', '1', '', '1', '9', ''), ('10', '9', 'Database/recovery', '数据库还原', '', '1', '1', '', '0', '10', ''), ('11', '2', 'Update/update', '在线升级', 'menu-icon fa fa-caret-right', '1', '1', '', '0', '11', ''), ('12', '2', 'Update/devlog', '开发日志', 'menu-icon fa fa-caret-right', '1', '1', '', '0', '12', ''), ('13', '0', '', '用户及组', 'menu-icon fa fa-users', '1', '1', '', '1', '13', ''), ('14', '13', 'Adminuser/index', '用户管理', 'menu-icon fa fa-caret-right', '1', '1', '', '1', '14', ''), ('15', '13', 'Adminuser/add', '新增用户', 'menu-icon fa fa-caret-right', '1', '1', '', '1', '15', ''), ('16', '13', 'Adminuser/edit', '编辑用户', 'menu-icon fa fa-caret-right', '1', '1', '', '0', '16', ''), ('17', '13', 'Adminuser/update', '保存用户', 'menu-icon fa fa-caret-right', '1', '1', '', '0', '17', ''), ('18', '13', 'Adminuser/del', '删除用户', '', '1', '1', '', '0', '18', ''), ('19', '13', 'Group/index', '用户组管理', 'menu-icon fa fa-caret-right', '1', '1', '', '1', '19', ''), ('20', '13', 'Group/add', '新增用户组', 'menu-icon fa fa-caret-right', '1', '1', '', '1', '20', ''), ('21', '13', 'Group/edit', '编辑用户组', 'menu-icon fa fa-caret-right', '1', '1', '', '0', '21', ''), ('22', '13', 'Group/update', '保存用户组', 'menu-icon fa fa-caret-right', '1', '1', '', '0', '22', ''), ('23', '13', 'Group/del', '删除用户组', '', '1', '1', '', '0', '23', ''), ('48', '0', 'Personal/index', '个人中心', 'menu-icon fa fa-user', '1', '1', '', '1', '48', ''), ('49', '48', 'Personal/profile', '个人资料', 'menu-icon fa fa-user', '1', '1', '', '1', '49', ''), ('50', '48', 'Logout/index', '退出', '', '1', '1', '', '1', '50', ''), ('51', '9', 'Database/export', '备份', '', '1', '1', '', '0', '51', ''), ('52', '9', 'Database/optimize', '数据优化', '', '1', '1', '', '0', '52', ''), ('53', '9', 'Database/repair', '修复表', '', '1', '1', '', '0', '53', ''), ('54', '11', 'Update/updating', '升级安装', '', '1', '1', '', '0', '54', ''), ('55', '48', 'Personal/update', '资料保存', '', '1', '1', '', '0', '55', ''), ('56', '3', 'Setting/update', '设置保存', '', '1', '1', '', '0', '56', ''), ('57', '9', 'Database/del', '备份删除', '', '1', '1', '', '0', '57', ''), ('58', '2', 'variable/index', '自定义变量', '', '1', '1', '', '1', '0', ''), ('59', '58', 'variable/add', '新增变量', '', '1', '1', '', '0', '0', ''), ('60', '58', 'variable/edit', '编辑变量', '', '1', '1', '', '0', '0', ''), ('61', '58', 'variable/update', '保存变量', '', '1', '1', '', '0', '0', ''), ('62', '58', 'variable/del', '删除变量', '', '1', '1', '', '0', '0', '');
COMMIT;

-- ----------------------------
--  Table structure for `qw_cid`
-- ----------------------------
DROP TABLE IF EXISTS `qw_cid`;
CREATE TABLE `qw_cid` (
  `cid_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `cid_pid` int(10) DEFAULT '0' COMMENT '上级分类',
  `cid_name` varchar(32) NOT NULL COMMENT '分类名称',
  `cid_type` tinyint(1) DEFAULT '0' COMMENT '分类类型(1景点,2目的地，3路线,4节日，5酒店,6餐厅)',
  `cid_sort` int(10) DEFAULT '0' COMMENT '排序(从小到大)',
  `cid_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0禁用,1启用',
  PRIMARY KEY (`cid_id`)
) ENGINE=InnoDB AUTO_INCREMENT=61 DEFAULT CHARSET=utf8mb4 COMMENT='景点分类表';

-- ----------------------------
--  Records of `qw_cid`
-- ----------------------------
BEGIN;
INSERT INTO `qw_cid` VALUES ('1', '0', '景点分类0', '1', '0', '1'), ('2', '0', '景点分类1', '1', '1', '1'), ('3', '0', '景点分类2', '1', '2', '1'), ('4', '0', '景点分类3', '1', '3', '1'), ('5', '0', '景点分类4', '1', '4', '1'), ('6', '0', '景点分类5', '1', '5', '1'), ('7', '0', '景点分类6', '1', '6', '1'), ('8', '0', '景点分类7', '1', '7', '1'), ('9', '0', '景点分类8', '1', '8', '1'), ('10', '0', '景点分类9', '1', '9', '1'), ('11', '0', '目的地分类0', '2', '0', '1'), ('12', '0', '目的地分类1', '2', '1', '1'), ('13', '0', '目的地分类2', '2', '2', '1'), ('14', '0', '目的地分类3', '2', '3', '1'), ('15', '0', '目的地分类4', '2', '4', '1'), ('16', '0', '目的地分类5', '2', '5', '1'), ('17', '0', '目的地分类6', '2', '6', '1'), ('18', '0', '目的地分类7', '2', '7', '1'), ('19', '0', '目的地分类8', '2', '8', '1'), ('20', '0', '目的地分类9', '2', '9', '1'), ('21', '0', '路线分类0', '3', '0', '1'), ('22', '0', '路线分类1', '3', '1', '1'), ('23', '0', '路线分类2', '3', '2', '1'), ('24', '0', '路线分类3', '3', '3', '1'), ('25', '0', '路线分类4', '3', '4', '1'), ('26', '0', '路线分类5', '3', '5', '1'), ('27', '0', '路线分类6', '3', '6', '1'), ('28', '0', '路线分类7', '3', '7', '1'), ('29', '0', '路线分类8', '3', '8', '1'), ('30', '0', '路线分类9', '3', '9', '1'), ('31', '0', '节日分类0', '4', '0', '1'), ('32', '0', '节日分类1', '4', '1', '1'), ('33', '0', '节日分类2', '4', '2', '1'), ('34', '0', '节日分类3', '4', '3', '1'), ('35', '0', '节日分类4', '4', '4', '1'), ('36', '0', '节日分类5', '4', '5', '1'), ('37', '0', '节日分类6', '4', '6', '1'), ('38', '0', '节日分类7', '4', '7', '1'), ('39', '0', '节日分类8', '4', '8', '1'), ('40', '0', '节日分类9', '4', '9', '1'), ('41', '0', '酒店分类0', '5', '0', '1'), ('42', '0', '酒店分类1', '5', '1', '1'), ('43', '0', '酒店分类2', '5', '2', '1'), ('44', '0', '酒店分类3', '5', '3', '1'), ('45', '0', '酒店分类4', '5', '4', '1'), ('46', '0', '酒店分类5', '5', '5', '1'), ('47', '0', '酒店分类6', '5', '6', '1'), ('48', '0', '酒店分类7', '5', '7', '1'), ('49', '0', '酒店分类8', '5', '8', '1'), ('50', '0', '酒店分类9', '5', '9', '1'), ('51', '0', '餐厅分类0', '6', '0', '1'), ('52', '0', '餐厅分类1', '6', '1', '1'), ('53', '0', '餐厅分类2', '6', '2', '1'), ('54', '0', '餐厅分类3', '6', '3', '1'), ('55', '0', '餐厅分类4', '6', '4', '1'), ('56', '0', '餐厅分类5', '6', '5', '1'), ('57', '0', '餐厅分类6', '6', '6', '1'), ('58', '0', '餐厅分类7', '6', '7', '1'), ('59', '0', '餐厅分类8', '6', '8', '1'), ('60', '0', '餐厅分类9', '6', '9', '1');
COMMIT;

-- ----------------------------
--  Table structure for `qw_cid_map`
-- ----------------------------
DROP TABLE IF EXISTS `qw_cid_map`;
CREATE TABLE `qw_cid_map` (
  `cid_map_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `cid_id` int(10) DEFAULT '0' COMMENT '分类id',
  `join_id` int(10) DEFAULT '0' COMMENT '关联id(如景点,目的地等)',
  `cid_map_sort` int(11) DEFAULT '0' COMMENT '排序',
  `cid_map_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1景点,2目的地，3路线,4节日，5酒店,6餐厅',
  PRIMARY KEY (`cid_map_id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COMMENT='分类引用表';

-- ----------------------------
--  Records of `qw_cid_map`
-- ----------------------------
BEGIN;
INSERT INTO `qw_cid_map` VALUES ('1', '1', '1', '1', '1'), ('2', '2', '2', '2', '1'), ('3', '3', '3', '3', '1'), ('4', '4', '4', '4', '1'), ('5', '5', '5', '5', '1'), ('6', '6', '6', '6', '1'), ('7', '7', '7', '7', '1'), ('8', '8', '8', '8', '1'), ('9', '9', '9', '9', '1'), ('10', '10', '10', '10', '1'), ('11', '21', '1', '1', '3'), ('12', '22', '1', '2', '3'), ('13', '21', '2', '1', '3'), ('14', '24', '2', '2', '3'), ('15', '25', '3', '1', '3'), ('16', '26', '3', '2', '3');
COMMIT;

-- ----------------------------
--  Table structure for `qw_destination`
-- ----------------------------
DROP TABLE IF EXISTS `qw_destination`;
CREATE TABLE `qw_destination` (
  `destination_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `destination_name` varchar(32) NOT NULL COMMENT '名称',
  `destination_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0禁用,1启用',
  `destination_created_at` int(11) DEFAULT '0',
  `destination_updated_at` int(11) DEFAULT '0',
  PRIMARY KEY (`destination_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COMMENT='目的地表';

-- ----------------------------
--  Records of `qw_destination`
-- ----------------------------
BEGIN;
INSERT INTO `qw_destination` VALUES ('1', '目的地名称0', '1', '1510918322', '1510918322'), ('2', '目的地名称1', '0', '1510918322', '1510918322'), ('3', '目的地名称2', '0', '1510918322', '1510918322'), ('4', '目的地名称3', '0', '1510918322', '1510918322'), ('5', '目的地名称4', '0', '1510918322', '1510918322'), ('6', '目的地名称5', '0', '1510918322', '1510918322'), ('7', '目的地名称6', '0', '1510918322', '1510918322'), ('8', '目的地名称7', '0', '1510918322', '1510918322'), ('9', '目的地名称8', '0', '1510918322', '1510918322'), ('10', '目的地名称9', '0', '1510918322', '1510918322');
COMMIT;

-- ----------------------------
--  Table structure for `qw_destination_join`
-- ----------------------------
DROP TABLE IF EXISTS `qw_destination_join`;
CREATE TABLE `qw_destination_join` (
  `destination_join_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `destination_id` int(10) DEFAULT '0' COMMENT '目的地id',
  `join_id` int(10) DEFAULT '0' COMMENT '关联id(如景点,目的地等)',
  `destination_join_sort` int(11) DEFAULT '0' COMMENT '排序',
  `destination_join_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1景点,2路线,3酒店,4餐厅',
  PRIMARY KEY (`destination_join_id`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COMMENT='目的地相关信息表';

-- ----------------------------
--  Records of `qw_destination_join`
-- ----------------------------
BEGIN;
INSERT INTO `qw_destination_join` VALUES ('1', '1', '1', '1', '1'), ('2', '2', '2', '2', '1'), ('3', '3', '3', '3', '1'), ('4', '4', '4', '4', '1'), ('5', '5', '5', '5', '1'), ('6', '6', '6', '6', '1'), ('7', '7', '7', '7', '1'), ('8', '8', '8', '8', '1'), ('9', '9', '9', '9', '1'), ('10', '10', '10', '10', '1'), ('11', '1', '1', '1', '2'), ('12', '2', '2', '2', '2'), ('13', '3', '3', '3', '2'), ('14', '4', '4', '4', '2'), ('15', '5', '5', '5', '2'), ('16', '6', '6', '6', '2'), ('17', '7', '7', '7', '2'), ('18', '8', '8', '8', '2'), ('19', '9', '9', '9', '2'), ('20', '10', '10', '10', '2'), ('21', '1', '1', '1', '3'), ('22', '2', '2', '2', '3'), ('23', '3', '3', '3', '3'), ('24', '4', '4', '4', '3'), ('25', '5', '5', '5', '3'), ('26', '6', '6', '6', '3'), ('27', '7', '7', '7', '3'), ('28', '8', '8', '8', '3'), ('29', '9', '9', '9', '3'), ('30', '10', '10', '10', '3'), ('31', '1', '1', '1', '4'), ('32', '2', '2', '2', '4'), ('33', '3', '3', '3', '4'), ('34', '4', '4', '4', '4'), ('35', '5', '5', '5', '4'), ('36', '6', '6', '6', '4'), ('37', '7', '7', '7', '4'), ('38', '8', '8', '8', '4'), ('39', '9', '9', '9', '4'), ('40', '10', '10', '10', '4');
COMMIT;

-- ----------------------------
--  Table structure for `qw_devlog`
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
--  Records of `qw_devlog`
-- ----------------------------
BEGIN;
INSERT INTO `qw_devlog` VALUES ('1', '1.0.0', '2016', '1440259200', 'ADMIN第一个版本发布。'), ('2', '1.0.1', '2016', '1440259200', '修改cookie过于简单的安全风险。');
COMMIT;

-- ----------------------------
--  Table structure for `qw_fav`
-- ----------------------------
DROP TABLE IF EXISTS `qw_fav`;
CREATE TABLE `qw_fav` (
  `fav_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `join_id` int(10) DEFAULT '0' COMMENT '关联id(如景点,目的地等)',
  `fav_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1景点,2节日，3酒店,4餐厅',
  `user_id` int(10) DEFAULT '0' COMMENT '创建者',
  `fav_created_at` int(11) DEFAULT '0',
  `fav_updated_at` int(11) DEFAULT '0',
  PRIMARY KEY (`fav_id`)
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=utf8mb4 COMMENT='收藏表';

-- ----------------------------
--  Records of `qw_fav`
-- ----------------------------
BEGIN;
INSERT INTO `qw_fav` VALUES ('2', '2', '1', '2', '1510918579', '1510918579'), ('3', '3', '1', '3', '1510918579', '1510918579'), ('4', '4', '1', '4', '1510918579', '1510918579'), ('5', '5', '1', '5', '1510918579', '1510918579'), ('6', '6', '1', '6', '1510918579', '1510918579'), ('7', '7', '1', '7', '1510918579', '1510918579'), ('8', '8', '1', '8', '1510918579', '1510918579'), ('9', '9', '1', '9', '1510918579', '1510918579'), ('10', '10', '1', '10', '1510918579', '1510918579'), ('11', '1', '2', '1', '1510918587', '1510918587'), ('12', '2', '2', '2', '1510918587', '1510918587'), ('13', '3', '2', '3', '1510918587', '1510918587'), ('14', '4', '2', '4', '1510918587', '1510918587'), ('15', '5', '2', '5', '1510918587', '1510918587'), ('16', '6', '2', '6', '1510918587', '1510918587'), ('17', '7', '2', '7', '1510918587', '1510918587'), ('18', '8', '2', '8', '1510918587', '1510918587'), ('19', '9', '2', '9', '1510918587', '1510918587'), ('20', '10', '2', '10', '1510918587', '1510918587'), ('21', '1', '3', '1', '1510918591', '1510918591'), ('22', '2', '3', '2', '1510918591', '1510918591'), ('23', '3', '3', '3', '1510918591', '1510918591'), ('24', '4', '3', '4', '1510918591', '1510918591'), ('25', '5', '3', '5', '1510918591', '1510918591'), ('26', '6', '3', '6', '1510918591', '1510918591'), ('27', '7', '3', '7', '1510918591', '1510918591'), ('28', '8', '3', '8', '1510918591', '1510918591'), ('29', '9', '3', '9', '1510918591', '1510918591'), ('30', '10', '3', '10', '1510918591', '1510918591'), ('31', '1', '4', '1', '1510918596', '1510918596'), ('32', '2', '4', '2', '1510918596', '1510918596'), ('33', '3', '4', '3', '1510918596', '1510918596'), ('34', '4', '4', '4', '1510918596', '1510918596'), ('35', '5', '4', '5', '1510918596', '1510918596'), ('36', '6', '4', '6', '1510918596', '1510918596'), ('37', '7', '4', '7', '1510918596', '1510918596'), ('38', '8', '4', '8', '1510918596', '1510918596'), ('39', '9', '4', '9', '1510918596', '1510918596'), ('40', '10', '4', '10', '1510918596', '1510918596');
COMMIT;

-- ----------------------------
--  Table structure for `qw_hall`
-- ----------------------------
DROP TABLE IF EXISTS `qw_hall`;
CREATE TABLE `qw_hall` (
  `hall_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `hall_name` varchar(32) NOT NULL COMMENT '名称',
  `hall_address` varchar(255) NOT NULL COMMENT '地址',
  `hall_phone` varchar(32) NOT NULL COMMENT '电话',
  `hall_price` decimal(10,2) DEFAULT '0.00' COMMENT '价格',
  `hall_intro` varchar(1000) NOT NULL COMMENT '介绍',
  `hall_score` decimal(10,2) DEFAULT '0.00' COMMENT '评分',
  `hall_evaluation` decimal(10,2) DEFAULT '0.00' COMMENT '评价',
  `hall_lon` varchar(64) DEFAULT '' COMMENT '经度',
  `hall_lat` varchar(64) DEFAULT '' COMMENT '纬度',
  `hall_geohash` varchar(255) DEFAULT '' COMMENT '通过经纬度换算得到的字符串索引',
  `hall_start_at` int(11) DEFAULT '0' COMMENT '开放时间',
  `hall_end_at` int(11) DEFAULT '0' COMMENT '结束时间',
  `hall_sort` int(10) DEFAULT '0' COMMENT '排序(从小到大)',
  `hall_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0禁用,1启用',
  `hall_created_at` int(11) DEFAULT '0',
  `hall_updated_at` int(11) DEFAULT '0',
  `hall_score_num` int(10) DEFAULT '0' COMMENT '评价数(目的地详情页需要用)',
  PRIMARY KEY (`hall_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COMMENT='餐厅表';

-- ----------------------------
--  Records of `qw_hall`
-- ----------------------------
BEGIN;
INSERT INTO `qw_hall` VALUES ('1', '餐厅名称0', '地址0', '0571-45648970', '250.89', '介绍0', '62.30', '8.30', '30.546566', '100.075546', '', '1510919589', '1510929589', '1', '1', '1510919589', '1510919589', '0'), ('2', '餐厅名称1', '地址1', '0571-45648971', '251.89', '介绍1', '62.31', '8.31', '31.546566', '101.075546', '', '1510919588', '1510929590', '2', '1', '1510919589', '1510919589', '0'), ('3', '餐厅名称2', '地址2', '0571-45648972', '252.89', '介绍2', '62.32', '8.32', '32.546566', '102.075546', '', '1510919587', '1510929591', '3', '1', '1510919589', '1510919589', '0'), ('4', '餐厅名称3', '地址3', '0571-45648973', '253.89', '介绍3', '62.33', '8.33', '33.546566', '103.075546', '', '1510919586', '1510929592', '4', '1', '1510919589', '1510919589', '0'), ('5', '餐厅名称4', '地址4', '0571-45648974', '254.89', '介绍4', '62.34', '8.34', '34.546566', '104.075546', '', '1510919585', '1510929593', '5', '1', '1510919589', '1510919589', '0'), ('6', '餐厅名称5', '地址5', '0571-45648975', '255.89', '介绍5', '62.35', '8.35', '35.546566', '105.075546', '', '1510919584', '1510929594', '6', '1', '1510919589', '1510919589', '0'), ('7', '餐厅名称6', '地址6', '0571-45648976', '256.89', '介绍6', '62.36', '8.36', '36.546566', '106.075546', '', '1510919583', '1510929595', '7', '1', '1510919589', '1510919589', '0'), ('8', '餐厅名称7', '地址7', '0571-45648977', '257.89', '介绍7', '62.37', '8.37', '37.546566', '107.075546', '', '1510919582', '1510929596', '8', '1', '1510919589', '1510919589', '0'), ('9', '餐厅名称8', '地址8', '0571-45648978', '258.89', '介绍8', '62.38', '8.38', '38.546566', '108.075546', '', '1510919581', '1510929597', '9', '1', '1510919589', '1510919589', '0'), ('10', '餐厅名称9', '地址9', '0571-45648979', '259.89', '介绍9', '62.39', '8.39', '39.546566', '109.075546', '', '1510919580', '1510929598', '10', '1', '1510919589', '1510919589', '0');
COMMIT;

-- ----------------------------
--  Table structure for `qw_holiday`
-- ----------------------------
DROP TABLE IF EXISTS `qw_holiday`;
CREATE TABLE `qw_holiday` (
  `holiday_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `shop_id` int(11) NOT NULL DEFAULT '0' COMMENT '商家id',
  `holiday_name` varchar(32) NOT NULL COMMENT '名称',
  `holiday_address` varchar(255) NOT NULL COMMENT '地址',
  `holiday_phone` varchar(32) NOT NULL COMMENT '电话',
  `holiday_price` decimal(10,2) DEFAULT '0.00' COMMENT '价格',
  `holiday_intro` varchar(1000) NOT NULL COMMENT '介绍',
  `holiday_score` decimal(10,2) DEFAULT '0.00' COMMENT '评分',
  `holiday_evaluation` decimal(10,2) DEFAULT '0.00' COMMENT '评价',
  `holiday_lon` varchar(64) DEFAULT '' COMMENT '经度',
  `holiday_lat` varchar(64) DEFAULT '' COMMENT '纬度',
  `holiday_geohash` varchar(255) DEFAULT '' COMMENT '通过经纬度换算得到的字符串索引',
  `holiday_start_at` int(11) DEFAULT '0' COMMENT '开放时间',
  `holiday_end_at` int(11) DEFAULT '0' COMMENT '结束时间',
  `holiday_sort` int(10) DEFAULT '0' COMMENT '排序(从小到大)',
  `holiday_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0禁用,1启用',
  `holiday_created_at` int(11) DEFAULT '0',
  `holiday_updated_at` int(11) DEFAULT '0',
  `holiday_suggest` varchar(64) DEFAULT '' COMMENT '建议游玩',
  PRIMARY KEY (`holiday_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COMMENT='节日表';

-- ----------------------------
--  Records of `qw_holiday`
-- ----------------------------
BEGIN;
INSERT INTO `qw_holiday` VALUES ('1', '0', '节日名称0', '地址0', '0571-45648970', '250.89', '介绍0', '62.30', '8.30', '30.546566', '100.075546', '', '1510919729', '1510929729', '1', '1', '1510919729', '1510919729', ''), ('2', '0', '节日名称1', '地址1', '0571-45648971', '251.89', '介绍1', '62.31', '8.31', '31.546566', '101.075546', '', '1510919728', '1510929730', '2', '1', '1510919729', '1510919729', ''), ('3', '0', '节日名称2', '地址2', '0571-45648972', '252.89', '介绍2', '62.32', '8.32', '32.546566', '102.075546', '', '1510919727', '1510929731', '3', '1', '1510919729', '1510919729', ''), ('4', '0', '节日名称3', '地址3', '0571-45648973', '253.89', '介绍3', '62.33', '8.33', '33.546566', '103.075546', '', '1510919726', '1510929732', '4', '1', '1510919729', '1510919729', ''), ('5', '0', '节日名称4', '地址4', '0571-45648974', '254.89', '介绍4', '62.34', '8.34', '34.546566', '104.075546', '', '1510919725', '1510929733', '5', '1', '1510919729', '1510919729', ''), ('6', '0', '节日名称5', '地址5', '0571-45648975', '255.89', '介绍5', '62.35', '8.35', '35.546566', '105.075546', '', '1510919724', '1510929734', '6', '1', '1510919729', '1510919729', ''), ('7', '0', '节日名称6', '地址6', '0571-45648976', '256.89', '介绍6', '62.36', '8.36', '36.546566', '106.075546', '', '1510919723', '1510929735', '7', '1', '1510919729', '1510919729', ''), ('8', '0', '节日名称7', '地址7', '0571-45648977', '257.89', '介绍7', '62.37', '8.37', '37.546566', '107.075546', '', '1510919722', '1510929736', '8', '1', '1510919729', '1510919729', ''), ('9', '0', '节日名称8', '地址8', '0571-45648978', '258.89', '介绍8', '62.38', '8.38', '38.546566', '108.075546', '', '1510919721', '1510929737', '9', '1', '1510919729', '1510919729', ''), ('10', '0', '节日名称9', '地址9', '0571-45648979', '259.89', '介绍9', '62.39', '8.39', '39.546566', '109.075546', '', '1510919720', '1510929738', '10', '1', '1510919729', '1510919729', '');
COMMIT;

-- ----------------------------
--  Table structure for `qw_home_page`
-- ----------------------------
DROP TABLE IF EXISTS `qw_home_page`;
CREATE TABLE `qw_home_page` (
  `home_page_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '页面id',
  `home_page_name` varchar(50) NOT NULL COMMENT '页面名称',
  `home_page_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '首页框框状态，1开启，0关闭',
  `home_page_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '页面类型，1首页广告 2首页热门线路 3首页热门目的地 4首页景点分类 5首页热门景点 6首页节日 7首页推荐周边',
  `home_page_sort` int(11) NOT NULL DEFAULT '1' COMMENT '排序，小在前',
  PRIMARY KEY (`home_page_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COMMENT='首页控制开关';

-- ----------------------------
--  Records of `qw_home_page`
-- ----------------------------
BEGIN;
INSERT INTO `qw_home_page` VALUES ('1', '首页广告', '1', '1', '1'), ('2', '首页热门线路', '1', '2', '2'), ('3', '首页热门目的地', '1', '3', '3'), ('4', '首页景点分类', '1', '4', '4'), ('5', '首页热门景点', '1', '5', '5'), ('6', '首页节日', '1', '6', '6'), ('7', '首页推荐周边', '1', '7', '7');
COMMIT;

-- ----------------------------
--  Table structure for `qw_home_page_value`
-- ----------------------------
DROP TABLE IF EXISTS `qw_home_page_value`;
CREATE TABLE `qw_home_page_value` (
  `home_page_value_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '记录id',
  `home_page_id` int(11) NOT NULL,
  `value_id` int(11) NOT NULL COMMENT '首页分类对应的值id',
  `sort` int(11) NOT NULL DEFAULT '1' COMMENT '排序，小的在前面',
  PRIMARY KEY (`home_page_value_id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8 COMMENT='首页控制对应的值';

-- ----------------------------
--  Records of `qw_home_page_value`
-- ----------------------------
BEGIN;
INSERT INTO `qw_home_page_value` VALUES ('1', '1', '11', '1'), ('2', '1', '12', '2'), ('3', '2', '1', '1'), ('4', '2', '2', '2'), ('5', '2', '3', '3'), ('6', '3', '1', '1'), ('7', '3', '2', '2'), ('8', '3', '3', '3'), ('9', '4', '1', '1'), ('10', '4', '2', '2'), ('11', '4', '3', '3'), ('12', '4', '4', '4'), ('13', '4', '5', '5'), ('14', '4', '6', '6'), ('15', '5', '1', '1'), ('16', '5', '2', '2'), ('17', '5', '3', '3'), ('18', '6', '1', '1'), ('19', '6', '2', '2'), ('20', '6', '3', '3'), ('21', '7', '1', '1'), ('22', '7', '2', '2'), ('23', '7', '3', '3'), ('24', '1', '13', '3'), ('25', '1', '14', '4'), ('26', '1', '15', '5');
COMMIT;

-- ----------------------------
--  Table structure for `qw_hotel`
-- ----------------------------
DROP TABLE IF EXISTS `qw_hotel`;
CREATE TABLE `qw_hotel` (
  `hotel_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `hotel_name` varchar(32) NOT NULL COMMENT '名称',
  `hotel_address` varchar(255) NOT NULL COMMENT '地址',
  `hotel_phone` varchar(32) NOT NULL COMMENT '电话',
  `hotel_price` decimal(10,2) DEFAULT '0.00' COMMENT '价格',
  `hotel_intro` varchar(1000) NOT NULL COMMENT '介绍',
  `hotel_score` decimal(10,2) DEFAULT '0.00' COMMENT '评分',
  `hotel_evaluation` decimal(10,2) DEFAULT '0.00' COMMENT '评价',
  `hotel_lon` varchar(64) DEFAULT '' COMMENT '经度',
  `hotel_lat` varchar(64) DEFAULT '' COMMENT '纬度',
  `hotel_geohash` varchar(255) DEFAULT '' COMMENT '通过经纬度换算得到的字符串索引',
  `hotel_start_at` int(11) DEFAULT '0' COMMENT '开放时间',
  `hotel_end_at` int(11) DEFAULT '0' COMMENT '结束时间',
  `hotel_sort` int(10) DEFAULT '0' COMMENT '排序(从小到大)',
  `hotel_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0禁用,1启用',
  `hotel_created_at` int(11) DEFAULT '0',
  `hotel_updated_at` int(11) DEFAULT '0',
  `hotel_score_num` int(10) DEFAULT '0' COMMENT '评价数(目的地详情页需要用)',
  PRIMARY KEY (`hotel_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COMMENT='酒店表';

-- ----------------------------
--  Records of `qw_hotel`
-- ----------------------------
BEGIN;
INSERT INTO `qw_hotel` VALUES ('1', '酒店名称0', '地址0', '0571-45648970', '250.89', '介绍0', '62.30', '8.30', '30.546566', '100.075546', '', '1510919801', '1510929801', '1', '1', '1510919801', '1510919801', '0'), ('2', '酒店名称1', '地址1', '0571-45648971', '251.89', '介绍1', '62.31', '8.31', '31.546566', '101.075546', '', '1510919800', '1510929802', '2', '1', '1510919801', '1510919801', '0'), ('3', '酒店名称2', '地址2', '0571-45648972', '252.89', '介绍2', '62.32', '8.32', '32.546566', '102.075546', '', '1510919799', '1510929803', '3', '1', '1510919801', '1510919801', '0'), ('4', '酒店名称3', '地址3', '0571-45648973', '253.89', '介绍3', '62.33', '8.33', '33.546566', '103.075546', '', '1510919798', '1510929804', '4', '1', '1510919801', '1510919801', '0'), ('5', '酒店名称4', '地址4', '0571-45648974', '254.89', '介绍4', '62.34', '8.34', '34.546566', '104.075546', '', '1510919797', '1510929805', '5', '1', '1510919801', '1510919801', '0'), ('6', '酒店名称5', '地址5', '0571-45648975', '255.89', '介绍5', '62.35', '8.35', '35.546566', '105.075546', '', '1510919796', '1510929806', '6', '1', '1510919801', '1510919801', '0'), ('7', '酒店名称6', '地址6', '0571-45648976', '256.89', '介绍6', '62.36', '8.36', '36.546566', '106.075546', '', '1510919795', '1510929807', '7', '1', '1510919801', '1510919801', '0'), ('8', '酒店名称7', '地址7', '0571-45648977', '257.89', '介绍7', '62.37', '8.37', '37.546566', '107.075546', '', '1510919794', '1510929808', '8', '1', '1510919801', '1510919801', '0'), ('9', '酒店名称8', '地址8', '0571-45648978', '258.89', '介绍8', '62.38', '8.38', '38.546566', '108.075546', '', '1510919793', '1510929809', '9', '1', '1510919801', '1510919801', '0'), ('10', '酒店名称9', '地址9', '0571-45648979', '259.89', '介绍9', '62.39', '8.39', '39.546566', '109.075546', '', '1510919792', '1510929810', '10', '1', '1510919801', '1510919801', '0');
COMMIT;

-- ----------------------------
--  Table structure for `qw_img`
-- ----------------------------
DROP TABLE IF EXISTS `qw_img`;
CREATE TABLE `qw_img` (
  `img_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `join_id` int(10) DEFAULT '0' COMMENT '关联id(如景点,目的地等)',
  `img_sort` int(11) DEFAULT '0' COMMENT '排序',
  `img_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1景点,2节日，3酒店,4餐厅,5评价',
  `img_url` varchar(255) NOT NULL,
  `img_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0禁用,1启用',
  `img_created_at` int(11) DEFAULT '0',
  `img_updated_at` int(11) DEFAULT '0',
  PRIMARY KEY (`img_id`)
) ENGINE=InnoDB AUTO_INCREMENT=61 DEFAULT CHARSET=utf8mb4 COMMENT='业务图片表';

-- ----------------------------
--  Records of `qw_img`
-- ----------------------------
BEGIN;
INSERT INTO `qw_img` VALUES ('1', '1', '1', '1', 'https://ss1.baidu.com/6ONXsjip0QIZ8tyhnq/it/u=924140812,899141053&fm=58', '1', '1510919975', '1510919975'), ('2', '2', '2', '1', 'https://ss1.baidu.com/6ONXsjip0QIZ8tyhnq/it/u=924140812,899141053&fm=58', '1', '1510919975', '1510919975'), ('3', '3', '3', '1', 'https://ss1.baidu.com/6ONXsjip0QIZ8tyhnq/it/u=924140812,899141053&fm=58', '1', '1510919975', '1510919975'), ('4', '4', '4', '1', 'https://ss1.baidu.com/6ONXsjip0QIZ8tyhnq/it/u=924140812,899141053&fm=58', '1', '1510919975', '1510919975'), ('5', '5', '5', '1', 'https://ss1.baidu.com/6ONXsjip0QIZ8tyhnq/it/u=924140812,899141053&fm=58', '1', '1510919975', '1510919975'), ('6', '6', '6', '1', 'https://ss1.baidu.com/6ONXsjip0QIZ8tyhnq/it/u=924140812,899141053&fm=58', '1', '1510919975', '1510919975'), ('7', '7', '7', '1', 'https://ss1.baidu.com/6ONXsjip0QIZ8tyhnq/it/u=924140812,899141053&fm=58', '1', '1510919975', '1510919975'), ('8', '8', '8', '1', 'https://ss1.baidu.com/6ONXsjip0QIZ8tyhnq/it/u=924140812,899141053&fm=58', '1', '1510919975', '1510919975'), ('9', '9', '9', '1', 'https://ss1.baidu.com/6ONXsjip0QIZ8tyhnq/it/u=924140812,899141053&fm=58', '1', '1510919975', '1510919975'), ('10', '10', '10', '1', 'https://ss1.baidu.com/6ONXsjip0QIZ8tyhnq/it/u=924140812,899141053&fm=58', '1', '1510919975', '1510919975'), ('11', '1', '1', '2', 'https://ss0.baidu.com/73t1bjeh1BF3odCf/it/u=379444388,2417571211&fm=85&s=86E049A30AB388CE14B0219003005099', '1', '1510920175', '1510920175'), ('12', '2', '2', '2', 'https://ss0.baidu.com/73t1bjeh1BF3odCf/it/u=379444388,2417571211&fm=85&s=86E049A30AB388CE14B0219003005099', '1', '1510920175', '1510920175'), ('13', '3', '3', '2', 'https://ss0.baidu.com/73t1bjeh1BF3odCf/it/u=379444388,2417571211&fm=85&s=86E049A30AB388CE14B0219003005099', '1', '1510920175', '1510920175'), ('14', '4', '4', '2', 'https://ss0.baidu.com/73t1bjeh1BF3odCf/it/u=379444388,2417571211&fm=85&s=86E049A30AB388CE14B0219003005099', '1', '1510920175', '1510920175'), ('15', '5', '5', '2', 'https://ss0.baidu.com/73t1bjeh1BF3odCf/it/u=379444388,2417571211&fm=85&s=86E049A30AB388CE14B0219003005099', '1', '1510920175', '1510920175'), ('16', '6', '6', '2', 'https://ss0.baidu.com/73t1bjeh1BF3odCf/it/u=379444388,2417571211&fm=85&s=86E049A30AB388CE14B0219003005099', '1', '1510920175', '1510920175'), ('17', '7', '7', '2', 'https://ss0.baidu.com/73t1bjeh1BF3odCf/it/u=379444388,2417571211&fm=85&s=86E049A30AB388CE14B0219003005099', '1', '1510920175', '1510920175'), ('18', '8', '8', '2', 'https://ss0.baidu.com/73t1bjeh1BF3odCf/it/u=379444388,2417571211&fm=85&s=86E049A30AB388CE14B0219003005099', '1', '1510920175', '1510920175'), ('19', '9', '9', '2', 'https://ss0.baidu.com/73t1bjeh1BF3odCf/it/u=379444388,2417571211&fm=85&s=86E049A30AB388CE14B0219003005099', '1', '1510920175', '1510920175'), ('20', '10', '10', '2', 'https://ss0.baidu.com/73t1bjeh1BF3odCf/it/u=379444388,2417571211&fm=85&s=86E049A30AB388CE14B0219003005099', '1', '1510920175', '1510920175'), ('31', '1', '1', '4', 'https://ss1.bdstatic.com/70cFuXSh_Q1YnxGkpoWK1HF6hhy/it/u=2928448178,3722104549&fm=200&gp=0.jpg', '1', '1510920222', '1510920222'), ('32', '2', '2', '4', 'https://ss1.bdstatic.com/70cFuXSh_Q1YnxGkpoWK1HF6hhy/it/u=2928448178,3722104549&fm=200&gp=0.jpg', '1', '1510920222', '1510920222'), ('33', '3', '3', '4', 'https://ss1.bdstatic.com/70cFuXSh_Q1YnxGkpoWK1HF6hhy/it/u=2928448178,3722104549&fm=200&gp=0.jpg', '1', '1510920222', '1510920222'), ('34', '4', '4', '4', 'https://ss1.bdstatic.com/70cFuXSh_Q1YnxGkpoWK1HF6hhy/it/u=2928448178,3722104549&fm=200&gp=0.jpg', '1', '1510920222', '1510920222'), ('35', '5', '5', '4', 'https://ss1.bdstatic.com/70cFuXSh_Q1YnxGkpoWK1HF6hhy/it/u=2928448178,3722104549&fm=200&gp=0.jpg', '1', '1510920222', '1510920222'), ('36', '6', '6', '4', 'https://ss1.bdstatic.com/70cFuXSh_Q1YnxGkpoWK1HF6hhy/it/u=2928448178,3722104549&fm=200&gp=0.jpg', '1', '1510920222', '1510920222'), ('37', '7', '7', '4', 'https://ss1.bdstatic.com/70cFuXSh_Q1YnxGkpoWK1HF6hhy/it/u=2928448178,3722104549&fm=200&gp=0.jpg', '1', '1510920222', '1510920222'), ('38', '8', '8', '4', 'https://ss1.bdstatic.com/70cFuXSh_Q1YnxGkpoWK1HF6hhy/it/u=2928448178,3722104549&fm=200&gp=0.jpg', '1', '1510920222', '1510920222'), ('39', '9', '9', '4', 'https://ss1.bdstatic.com/70cFuXSh_Q1YnxGkpoWK1HF6hhy/it/u=2928448178,3722104549&fm=200&gp=0.jpg', '1', '1510920222', '1510920222'), ('40', '10', '10', '4', 'https://ss1.bdstatic.com/70cFuXSh_Q1YnxGkpoWK1HF6hhy/it/u=2928448178,3722104549&fm=200&gp=0.jpg', '1', '1510920222', '1510920222'), ('41', '1', '1', '3', 'https://ss2.baidu.com/6ONYsjip0QIZ8tyhnq/it/u=2223288917,1646701773&fm=58&bpow=1600&bpoh=1200&u_exp_0=2036309813,1329934640&fm_exp_0=86', '1', '1510920263', '1510920263'), ('42', '2', '2', '3', 'https://ss2.baidu.com/6ONYsjip0QIZ8tyhnq/it/u=2223288917,1646701773&fm=58&bpow=1600&bpoh=1200&u_exp_0=2036309813,1329934640&fm_exp_0=86', '1', '1510920263', '1510920263'), ('43', '3', '3', '3', 'https://ss2.baidu.com/6ONYsjip0QIZ8tyhnq/it/u=2223288917,1646701773&fm=58&bpow=1600&bpoh=1200&u_exp_0=2036309813,1329934640&fm_exp_0=86', '1', '1510920263', '1510920263'), ('44', '4', '4', '3', 'https://ss2.baidu.com/6ONYsjip0QIZ8tyhnq/it/u=2223288917,1646701773&fm=58&bpow=1600&bpoh=1200&u_exp_0=2036309813,1329934640&fm_exp_0=86', '1', '1510920263', '1510920263'), ('45', '5', '5', '3', 'https://ss2.baidu.com/6ONYsjip0QIZ8tyhnq/it/u=2223288917,1646701773&fm=58&bpow=1600&bpoh=1200&u_exp_0=2036309813,1329934640&fm_exp_0=86', '1', '1510920263', '1510920263'), ('46', '6', '6', '3', 'https://ss2.baidu.com/6ONYsjip0QIZ8tyhnq/it/u=2223288917,1646701773&fm=58&bpow=1600&bpoh=1200&u_exp_0=2036309813,1329934640&fm_exp_0=86', '1', '1510920263', '1510920263'), ('47', '7', '7', '3', 'https://ss2.baidu.com/6ONYsjip0QIZ8tyhnq/it/u=2223288917,1646701773&fm=58&bpow=1600&bpoh=1200&u_exp_0=2036309813,1329934640&fm_exp_0=86', '1', '1510920263', '1510920263'), ('48', '8', '8', '3', 'https://ss2.baidu.com/6ONYsjip0QIZ8tyhnq/it/u=2223288917,1646701773&fm=58&bpow=1600&bpoh=1200&u_exp_0=2036309813,1329934640&fm_exp_0=86', '1', '1510920263', '1510920263'), ('49', '9', '9', '3', 'https://ss2.baidu.com/6ONYsjip0QIZ8tyhnq/it/u=2223288917,1646701773&fm=58&bpow=1600&bpoh=1200&u_exp_0=2036309813,1329934640&fm_exp_0=86', '1', '1510920263', '1510920263'), ('50', '10', '10', '3', 'https://ss2.baidu.com/6ONYsjip0QIZ8tyhnq/it/u=2223288917,1646701773&fm=58&bpow=1600&bpoh=1200&u_exp_0=2036309813,1329934640&fm_exp_0=86', '1', '1510920263', '1510920263'), ('51', '1', '1', '5', 'https://ss2.baidu.com/6ONYsjip0QIZ8tyhnq/it/u=1140985205,3685509106&fm=58&u_exp_0=573951590,3352203696&fm_exp_0=86&bpow=650&bpoh=650', '1', '1510920287', '1510920287'), ('52', '2', '2', '5', 'https://ss2.baidu.com/6ONYsjip0QIZ8tyhnq/it/u=1140985205,3685509106&fm=58&u_exp_0=573951590,3352203696&fm_exp_0=86&bpow=650&bpoh=650', '1', '1510920287', '1510920287'), ('53', '3', '3', '5', 'https://ss2.baidu.com/6ONYsjip0QIZ8tyhnq/it/u=1140985205,3685509106&fm=58&u_exp_0=573951590,3352203696&fm_exp_0=86&bpow=650&bpoh=650', '1', '1510920287', '1510920287'), ('54', '4', '4', '5', 'https://ss2.baidu.com/6ONYsjip0QIZ8tyhnq/it/u=1140985205,3685509106&fm=58&u_exp_0=573951590,3352203696&fm_exp_0=86&bpow=650&bpoh=650', '1', '1510920287', '1510920287'), ('55', '5', '5', '5', 'https://ss2.baidu.com/6ONYsjip0QIZ8tyhnq/it/u=1140985205,3685509106&fm=58&u_exp_0=573951590,3352203696&fm_exp_0=86&bpow=650&bpoh=650', '1', '1510920287', '1510920287'), ('56', '6', '6', '5', 'https://ss2.baidu.com/6ONYsjip0QIZ8tyhnq/it/u=1140985205,3685509106&fm=58&u_exp_0=573951590,3352203696&fm_exp_0=86&bpow=650&bpoh=650', '1', '1510920287', '1510920287'), ('57', '7', '7', '5', 'https://ss2.baidu.com/6ONYsjip0QIZ8tyhnq/it/u=1140985205,3685509106&fm=58&u_exp_0=573951590,3352203696&fm_exp_0=86&bpow=650&bpoh=650', '1', '1510920287', '1510920287'), ('58', '8', '8', '5', 'https://ss2.baidu.com/6ONYsjip0QIZ8tyhnq/it/u=1140985205,3685509106&fm=58&u_exp_0=573951590,3352203696&fm_exp_0=86&bpow=650&bpoh=650', '1', '1510920287', '1510920287'), ('59', '9', '9', '5', 'https://ss2.baidu.com/6ONYsjip0QIZ8tyhnq/it/u=1140985205,3685509106&fm=58&u_exp_0=573951590,3352203696&fm_exp_0=86&bpow=650&bpoh=650', '1', '1510920287', '1510920287'), ('60', '10', '10', '5', 'https://ss2.baidu.com/6ONYsjip0QIZ8tyhnq/it/u=1140985205,3685509106&fm=58&u_exp_0=573951590,3352203696&fm_exp_0=86&bpow=650&bpoh=650', '1', '1510920287', '1510920287');
COMMIT;

-- ----------------------------
--  Table structure for `qw_message`
-- ----------------------------
DROP TABLE IF EXISTS `qw_message`;
CREATE TABLE `qw_message` (
  `message_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT '0' COMMENT '接收者id',
  `message_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1全部用户,2单个用户',
  `message_read` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0未读1已读',
  `message_comment` varchar(255) NOT NULL COMMENT '内容',
  `created_user_id` int(10) DEFAULT '0' COMMENT '创建者',
  `message_created_at` int(11) DEFAULT '0',
  `message_updated_at` int(11) DEFAULT '0',
  PRIMARY KEY (`message_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COMMENT='消息表';

-- ----------------------------
--  Records of `qw_message`
-- ----------------------------
BEGIN;
INSERT INTO `qw_message` VALUES ('1', '1', '2', '0', '1', '0', '1510920482', '1510920482'), ('2', '2', '2', '0', '1', '0', '1510920482', '1510920482'), ('3', '3', '2', '0', '1', '0', '1510920482', '1510920482'), ('4', '4', '2', '0', '1', '0', '1510920482', '1510920482'), ('5', '5', '2', '0', '1', '0', '1510920482', '1510920482'), ('6', '6', '2', '0', '1', '0', '1510920482', '1510920482'), ('7', '7', '2', '0', '1', '0', '1510920482', '1510920482'), ('8', '8', '2', '0', '1', '0', '1510920482', '1510920482'), ('9', '9', '2', '0', '1', '0', '1510920482', '1510920482'), ('10', '10', '2', '0', '1', '0', '1510920482', '1510920482');
COMMIT;

-- ----------------------------
--  Table structure for `qw_order`
-- ----------------------------
DROP TABLE IF EXISTS `qw_order`;
CREATE TABLE `qw_order` (
  `order_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `shop_id` int(11) NOT NULL DEFAULT '0' COMMENT '商家id',
  `order_sn` varchar(50) NOT NULL COMMENT '订单号',
  `join_id` int(10) DEFAULT '0' COMMENT '商品关联id(如景点,目的地等)',
  `order_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '订单类型1景点,2节日，3酒店,4餐厅',
  `order_num` int(10) DEFAULT '0' COMMENT '商品数量',
  `order_price` decimal(10,2) DEFAULT '0.00' COMMENT '商品单价',
  `order_amount` decimal(10,2) DEFAULT '0.00' COMMENT '商品总价',
  `order_pay_amount` decimal(10,2) DEFAULT '0.00' COMMENT '订单实付款',
  `order_refund_amount` decimal(10,2) DEFAULT '0.00' COMMENT '退款金额',
  `order_reward_amount` decimal(2,0) DEFAULT '0' COMMENT '奖励的红包金额',
  `order_is_score` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否评价,0未,1已',
  `order_status` tinyint(1) NOT NULL DEFAULT '10' COMMENT '订单状态(10未付款,20已支付，30已核销，40已评价，0未付款取消',
  `order_cancel_type` tinyint(1) DEFAULT '0' COMMENT '取消方式(1未付款手动取消,2未付款自动取消,3已付款手动取消[退款])',
  `user_id` int(10) NOT NULL DEFAULT '0' COMMENT '创建者',
  `order_pay_at` int(11) DEFAULT '0' COMMENT '支付时间',
  `order_refund_at` int(11) DEFAULT '0' COMMENT '退款时间',
  `order_cancel_at` int(11) DEFAULT '0' COMMENT '取消时间',
  `order_created_at` int(11) DEFAULT '0',
  `order_updated_at` int(11) DEFAULT '0',
  PRIMARY KEY (`order_id`)
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8mb4 COMMENT='订单表';

-- ----------------------------
--  Records of `qw_order`
-- ----------------------------
BEGIN;
INSERT INTO `qw_order` VALUES ('1', '0', '2017111749485252', '1', '1', '1', '250.89', '2500.89', '2500.89', '0.00', '0', '1', '10', '0', '1', '0', '0', '0', '1510921201', '1510921201'), ('2', '0', '2017111749485253', '2', '1', '2', '251.89', '2511.78', '2511.78', '0.00', '0', '1', '10', '0', '2', '0', '0', '0', '1510921201', '1510921201'), ('3', '0', '2017111749485254', '3', '1', '3', '252.89', '2522.67', '2522.67', '0.00', '0', '1', '10', '0', '3', '0', '0', '0', '1510921201', '1510921201'), ('4', '0', '2017111749485255', '4', '1', '4', '253.89', '2533.56', '2533.56', '0.00', '0', '1', '10', '0', '4', '0', '0', '0', '1510921201', '1510921201'), ('5', '0', '2017111749485256', '5', '1', '5', '254.89', '2544.45', '2544.45', '0.00', '0', '1', '10', '0', '5', '0', '0', '0', '1510921201', '1510921201'), ('6', '0', '2017111749485257', '6', '1', '6', '255.89', '2555.34', '2555.34', '0.00', '0', '1', '10', '0', '6', '0', '0', '0', '1510921201', '1510921201'), ('7', '0', '2017111749485297', '7', '1', '7', '256.89', '2566.23', '2566.23', '0.00', '0', '1', '10', '0', '7', '0', '0', '0', '1510921201', '1510921201'), ('8', '0', '2017111749485298', '8', '1', '8', '257.89', '2577.12', '2577.12', '0.00', '0', '1', '10', '0', '8', '0', '0', '0', '1510921201', '1510921201'), ('9', '0', '2017111749485210', '9', '1', '9', '258.89', '2588.01', '2588.01', '0.00', '0', '1', '10', '0', '9', '0', '0', '0', '1510921201', '1510921201'), ('10', '0', '2017111749485210', '10', '1', '10', '259.89', '2598.90', '2598.90', '0.00', '0', '1', '10', '0', '10', '0', '0', '0', '1510921201', '1510921201'), ('11', '0', '2017111798985455', '1', '1', '1', '250.89', '2500.89', '2500.89', '0.00', '0', '1', '20', '0', '1', '1510921339', '0', '0', '1510921339', '1510921339'), ('12', '0', '2017111798985748', '2', '1', '2', '251.89', '2511.78', '2511.78', '0.00', '0', '1', '20', '0', '2', '1510921339', '0', '0', '1510921339', '1510921339'), ('13', '0', '2017111798989748', '3', '1', '3', '252.89', '2522.67', '2522.67', '0.00', '0', '1', '20', '0', '3', '1510921339', '0', '0', '1510921339', '1510921339'), ('14', '0', '2017111798989749', '4', '1', '4', '253.89', '2533.56', '2533.56', '0.00', '0', '1', '20', '0', '4', '1510921339', '0', '0', '1510921339', '1510921339'), ('15', '0', '2017111798989751', '5', '1', '5', '254.89', '2544.45', '2544.45', '0.00', '0', '1', '20', '0', '5', '1510921339', '0', '0', '1510921339', '1510921339'), ('16', '0', '2017111798989951', '6', '1', '6', '255.89', '2555.34', '2555.34', '0.00', '0', '1', '20', '0', '6', '1510921339', '0', '0', '1510921339', '1510921339'), ('17', '0', '2017111798989952', '7', '1', '7', '256.89', '2566.23', '2566.23', '0.00', '0', '1', '20', '0', '7', '1510921339', '0', '0', '1510921339', '1510921339'), ('18', '0', '2017111798989953', '8', '1', '8', '257.89', '2577.12', '2577.12', '0.00', '0', '1', '20', '0', '8', '1510921339', '0', '0', '1510921339', '1510921339'), ('19', '0', '2017111798981014', '9', '1', '9', '258.89', '2588.01', '2588.01', '0.00', '0', '1', '20', '0', '9', '1510921339', '0', '0', '1510921339', '1510921339'), ('20', '0', '2017111798981015', '10', '1', '10', '259.89', '2598.90', '2598.90', '0.00', '0', '1', '20', '0', '10', '1510921339', '0', '0', '1510921339', '1510921339'), ('21', '0', '2017111751521001', '1', '1', '1', '250.89', '2500.89', '2500.89', '0.00', '0', '1', '30', '0', '1', '1510921907', '0', '0', '1510921907', '1510921907'), ('22', '0', '2017111751521014', '2', '1', '2', '251.89', '2511.78', '2511.78', '0.00', '0', '1', '30', '0', '2', '1510921907', '0', '0', '1510921907', '1510921907'), ('23', '0', '2017111751521014', '3', '1', '3', '252.89', '2522.67', '2522.67', '0.00', '0', '1', '30', '0', '3', '1510921907', '0', '0', '1510921907', '1510921907'), ('24', '0', '2017111751521015', '4', '1', '4', '253.89', '2533.56', '2533.56', '0.00', '0', '1', '30', '0', '4', '1510921907', '0', '0', '1510921907', '1510921907'), ('25', '0', '2017111751521015', '5', '1', '5', '254.89', '2544.45', '2544.45', '0.00', '0', '1', '30', '0', '5', '1510921907', '0', '0', '1510921907', '1510921907'), ('26', '0', '2017111751521015', '6', '1', '6', '255.89', '2555.34', '2555.34', '0.00', '0', '1', '30', '0', '6', '1510921907', '0', '0', '1510921907', '1510921907'), ('27', '0', '2017111751521015', '7', '1', '7', '256.89', '2566.23', '2566.23', '0.00', '0', '1', '30', '0', '7', '1510921907', '0', '0', '1510921907', '1510921907'), ('28', '0', '2017111751521015', '8', '1', '8', '257.89', '2577.12', '2577.12', '0.00', '0', '1', '30', '0', '8', '1510921907', '0', '0', '1510921907', '1510921907'), ('29', '0', '2017111751521015', '9', '1', '9', '258.89', '2588.01', '2588.01', '0.00', '0', '1', '30', '0', '9', '1510921907', '0', '0', '1510921907', '1510921907'), ('30', '0', '2017111751521015', '10', '1', '10', '259.89', '2598.90', '2598.90', '0.00', '0', '1', '30', '0', '10', '1510921907', '0', '0', '1510921907', '1510921907'), ('31', '0', '2017111751559752', '1', '1', '1', '250.89', '2500.89', '2500.89', '0.00', '0', '1', '40', '0', '1', '1510921955', '0', '0', '1510921955', '1510921955'), ('32', '0', '2017111751559753', '2', '1', '2', '251.89', '2511.78', '2511.78', '0.00', '0', '1', '40', '0', '2', '1510921955', '0', '0', '1510921955', '1510921955'), ('33', '0', '2017111751559754', '3', '1', '3', '252.89', '2522.67', '2522.67', '0.00', '0', '1', '40', '0', '3', '1510921955', '0', '0', '1510921955', '1510921955'), ('34', '0', '2017111751559755', '4', '1', '4', '253.89', '2533.56', '2533.56', '0.00', '0', '1', '40', '0', '4', '1510921955', '0', '0', '1510921955', '1510921955'), ('35', '0', '2017111751559756', '5', '1', '5', '254.89', '2544.45', '2544.45', '0.00', '0', '1', '40', '0', '5', '1510921955', '0', '0', '1510921955', '1510921955'), ('36', '0', '2017111751559757', '6', '1', '6', '255.89', '2555.34', '2555.34', '0.00', '0', '1', '40', '0', '6', '1510921955', '0', '0', '1510921955', '1510921955'), ('37', '0', '2017111751559797', '7', '1', '7', '256.89', '2566.23', '2566.23', '0.00', '0', '1', '40', '0', '7', '1510921955', '0', '0', '1510921955', '1510921955'), ('38', '0', '2017111751559798', '8', '1', '8', '257.89', '2577.12', '2577.12', '0.00', '0', '1', '40', '0', '8', '1510921955', '0', '0', '1510921955', '1510921955'), ('39', '0', '2017111751559799', '9', '1', '9', '258.89', '2588.01', '2588.01', '0.00', '0', '1', '40', '0', '9', '1510921955', '0', '0', '1510921955', '1510921955'), ('40', '0', '2017111751559710', '10', '1', '10', '259.89', '2598.90', '2598.90', '0.00', '0', '1', '40', '0', '10', '1510921955', '0', '0', '1510921955', '1510921955'), ('41', '0', '2017111799975454', '1', '1', '1', '250.89', '2500.89', '2500.89', '0.00', '0', '1', '0', '0', '1', '0', '0', '0', '1510922012', '1510922012'), ('42', '0', '2017111799975548', '2', '1', '2', '251.89', '2511.78', '2511.78', '0.00', '0', '1', '0', '0', '2', '0', '0', '0', '1510922012', '1510922012'), ('43', '0', '2017111799975556', '3', '1', '3', '252.89', '2522.67', '2522.67', '0.00', '0', '1', '0', '0', '3', '0', '0', '0', '1510922012', '1510922012'), ('44', '0', '2017111799975510', '4', '1', '4', '253.89', '2533.56', '2533.56', '0.00', '0', '1', '0', '0', '4', '0', '0', '0', '1510922012', '1510922012'), ('45', '0', '2017111799975649', '5', '1', '5', '254.89', '2544.45', '2544.45', '0.00', '0', '1', '0', '0', '5', '0', '0', '0', '1510922012', '1510922012'), ('46', '0', '2017111799975650', '6', '1', '6', '255.89', '2555.34', '2555.34', '0.00', '0', '1', '0', '0', '6', '0', '0', '0', '1510922012', '1510922012'), ('47', '0', '2017111799975610', '7', '1', '7', '256.89', '2566.23', '2566.23', '0.00', '0', '1', '0', '0', '7', '0', '0', '0', '1510922012', '1510922012'), ('48', '0', '2017111799975755', '8', '1', '8', '257.89', '2577.12', '2577.12', '0.00', '0', '1', '0', '0', '8', '0', '0', '0', '1510922012', '1510922012'), ('49', '0', '2017111799975710', '9', '1', '9', '258.89', '2588.01', '2588.01', '0.00', '0', '1', '0', '0', '9', '0', '0', '0', '1510922012', '1510922012'), ('50', '0', '2017111799979799', '10', '1', '10', '259.89', '2598.90', '2598.90', '0.00', '0', '1', '0', '0', '10', '0', '0', '0', '1510922012', '1510922012');
COMMIT;

-- ----------------------------
--  Table structure for `qw_order_code`
-- ----------------------------
DROP TABLE IF EXISTS `qw_order_code`;
CREATE TABLE `qw_order_code` (
  `order_code_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `shop_id` int(11) DEFAULT '0' COMMENT '商家id',
  `order_id` int(11) NOT NULL DEFAULT '0' COMMENT '订单id',
  `code` varchar(64) NOT NULL COMMENT '兑换码',
  `is_exchange` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否兑换核销,0未,1已',
  `exchange_user_id` int(10) DEFAULT '0' COMMENT '核销人员(管理员)',
  `exchange_at` int(10) NOT NULL DEFAULT '0' COMMENT '核销时间',
  `created_at` int(10) NOT NULL DEFAULT '0' COMMENT '添加时间',
  PRIMARY KEY (`order_code_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COMMENT='订单表';

-- ----------------------------
--  Records of `qw_order_code`
-- ----------------------------
BEGIN;
INSERT INTO `qw_order_code` VALUES ('1', '0', '1', '60c7-830d-486b-4e18', '1', '0', '0', '1510923413'), ('2', '0', '2', '21e9-a436-6737-79b0', '1', '0', '0', '1510923413'), ('3', '0', '3', 'd0c6-7bce-4779-86c0', '1', '0', '0', '1510923413'), ('4', '0', '4', 'a1f3-77d2-f4f0-4fe4', '1', '0', '0', '1510923413'), ('5', '0', '5', '13de-ff37-535d-1720', '1', '0', '0', '1510923413'), ('6', '0', '6', '78ab-4336-7606-98a8', '1', '0', '0', '1510923413'), ('7', '0', '7', 'b917-217c-a26b-c85e', '1', '0', '0', '1510923413'), ('8', '0', '8', '9eda-7f8c-096f-0dd2', '1', '0', '0', '1510923413'), ('9', '0', '9', '97f4-2213-a923-9182', '1', '0', '0', '1510923413'), ('10', '0', '10', 'f7eb-c2b3-7a2c-a307', '1', '0', '0', '1510923413');
COMMIT;

-- ----------------------------
--  Table structure for `qw_page_view`
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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COMMENT='用户行为表';

-- ----------------------------
--  Records of `qw_page_view`
-- ----------------------------
BEGIN;
INSERT INTO `qw_page_view` VALUES ('1', '0', '2016-09-14 16:55:11', '1473843311', 'Index', 'index', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/53.0.2785.101 Safari/537.36', 'http://www.resume.com/index.php?'), ('2', '0', '2017-11-13 18:11:01', '1510567861', 'Index', 'index', '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.89 Safari/537.36', 'http://admin.quwan.cn/'), ('3', '0', '2017-11-18 14:44:25', '1510987465', 'Index', 'index', '42.245.252.16', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.89 Safari/537.36', 'http://119.29.87.252:8801/');
COMMIT;

-- ----------------------------
--  Table structure for `qw_red_status`
-- ----------------------------
DROP TABLE IF EXISTS `qw_red_status`;
CREATE TABLE `qw_red_status` (
  `red_id` int(11) NOT NULL AUTO_INCREMENT,
  `red_status` tinyint(1) DEFAULT '1' COMMENT '1开启，0关闭',
  `red_start_num` int(11) DEFAULT '0' COMMENT '红包开始金额',
  `red_end_num` int(11) DEFAULT '0' COMMENT '红包结束金额',
  PRIMARY KEY (`red_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='红包开关表';

-- ----------------------------
--  Table structure for `qw_route`
-- ----------------------------
DROP TABLE IF EXISTS `qw_route`;
CREATE TABLE `qw_route` (
  `route_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `route_name` varchar(32) NOT NULL COMMENT '名称',
  `route_day_num` tinyint(1) NOT NULL DEFAULT '1' COMMENT '天数',
  `user_id` int(10) DEFAULT '0' COMMENT '线路创建者0是官方路线，不允许修改',
  `route_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0禁用,1启用',
  `route_created_at` int(11) DEFAULT '0',
  `route_updated_at` int(11) DEFAULT '0',
  `route_intro` varchar(1000) DEFAULT '' COMMENT '简介',
  `route_use_num` int(10) DEFAULT '0' COMMENT '使用次数(目的地详情页需要用)',
  PRIMARY KEY (`route_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COMMENT='路线表';

-- ----------------------------
--  Records of `qw_route`
-- ----------------------------
BEGIN;
INSERT INTO `qw_route` VALUES ('1', '线路1', '1', '0', '1', '1510923633', '1510923633', '线路简介1', '0'), ('2', '线路2', '2', '0', '1', '1510923633', '1510923633', '线路简2', '0'), ('3', '线路3', '3', '0', '1', '1510923633', '1510923633', '线路简介3', '0'), ('4', '线路4', '4', '0', '1', '1510923633', '1510923633', '线路简介4', '0'), ('5', '线路5', '5', '0', '1', '1510923633', '1510923633', '线路简介5', '0'), ('6', '线路6', '6', '0', '1', '1510923633', '1510923633', '线路简介6', '0'), ('7', '线路7', '7', '0', '1', '1510923633', '1510923633', '线路简介7', '0'), ('8', '线路8', '8', '0', '1', '1510923633', '1510923633', '线路简介8', '0'), ('9', '线路9', '9', '0', '1', '1510923633', '1510923633', '线路简介9', '0'), ('10', '线路10', '10', '0', '1', '1510923633', '1510923633', '线路简介10', '0');
COMMIT;

-- ----------------------------
--  Table structure for `qw_route_day`
-- ----------------------------
DROP TABLE IF EXISTS `qw_route_day`;
CREATE TABLE `qw_route_day` (
  `route_day_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `route_id` int(10) DEFAULT '0' COMMENT '目的地id',
  `route_day_intro` varchar(255) NOT NULL COMMENT '日程介绍',
  `route_day_sort` int(11) DEFAULT '0' COMMENT '排序，从小到大，小的在前面',
  PRIMARY KEY (`route_day_id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COMMENT='路线日程表';

-- ----------------------------
--  Records of `qw_route_day`
-- ----------------------------
BEGIN;
INSERT INTO `qw_route_day` VALUES ('1', '1', '1', '1'), ('2', '2', '1', '2'), ('3', '3', '1', '3'), ('4', '4', '1', '4'), ('5', '5', '1', '5'), ('6', '6', '1', '6'), ('7', '7', '1', '7'), ('8', '8', '1', '8'), ('9', '9', '1', '9'), ('10', '10', '1', '10'), ('11', '11', '1', '11'), ('12', '12', '1', '12'), ('13', '13', '1', '13'), ('14', '14', '1', '14'), ('15', '15', '1', '15'), ('16', '16', '1', '16'), ('17', '17', '1', '17'), ('18', '18', '1', '18'), ('19', '19', '1', '19'), ('20', '20', '1', '20');
COMMIT;

-- ----------------------------
--  Table structure for `qw_route_day_join`
-- ----------------------------
DROP TABLE IF EXISTS `qw_route_day_join`;
CREATE TABLE `qw_route_day_join` (
  `route_day_join_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `route_day_id` int(10) DEFAULT '0' COMMENT '线路日程id',
  `join_id` int(10) DEFAULT '0' COMMENT '关联id(如景点,目的地等)',
  `route_day_join_sort` int(11) DEFAULT '0' COMMENT '排序',
  `route_day_join_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1景点,2目的地，3节日，4酒店,5餐厅',
  `route_id` int(10) DEFAULT '0' COMMENT '线路id(方便取线路下的第一张图片)',
  PRIMARY KEY (`route_day_join_id`)
) ENGINE=InnoDB AUTO_INCREMENT=101 DEFAULT CHARSET=utf8mb4 COMMENT='路线日程相关数据表';

-- ----------------------------
--  Records of `qw_route_day_join`
-- ----------------------------
BEGIN;
INSERT INTO `qw_route_day_join` VALUES ('1', '1', '1', '1', '1', '1'), ('2', '2', '2', '2', '1', '1'), ('3', '3', '3', '3', '1', '1'), ('4', '4', '4', '4', '1', '1'), ('5', '5', '5', '5', '1', '1'), ('6', '6', '6', '6', '1', '1'), ('7', '7', '7', '7', '1', '1'), ('8', '8', '8', '8', '1', '1'), ('9', '9', '9', '9', '1', '1'), ('10', '10', '10', '10', '1', '1'), ('11', '11', '11', '11', '1', '1'), ('12', '12', '12', '12', '1', '1'), ('13', '13', '13', '13', '1', '1'), ('14', '14', '14', '14', '1', '1'), ('15', '15', '15', '15', '1', '1'), ('16', '16', '16', '16', '1', '1'), ('17', '17', '17', '17', '1', '1'), ('18', '18', '18', '18', '1', '1'), ('19', '19', '19', '19', '1', '1'), ('20', '20', '20', '20', '1', '1'), ('21', '1', '1', '1', '2', '2'), ('22', '2', '2', '2', '2', '2'), ('23', '3', '3', '3', '2', '2'), ('24', '4', '4', '4', '2', '2'), ('25', '5', '5', '5', '2', '2'), ('26', '6', '6', '6', '2', '2'), ('27', '7', '7', '7', '2', '2'), ('28', '8', '8', '8', '2', '22'), ('29', '9', '9', '9', '2', '2'), ('30', '10', '10', '10', '2', '2'), ('31', '11', '11', '11', '2', '2'), ('32', '12', '12', '12', '2', '2'), ('33', '13', '13', '13', '2', '2'), ('34', '14', '14', '14', '2', '2'), ('35', '15', '15', '15', '2', '2'), ('36', '16', '16', '16', '2', '2'), ('37', '17', '17', '17', '2', '2'), ('38', '18', '18', '18', '2', '2'), ('39', '19', '19', '19', '2', '2'), ('40', '20', '20', '20', '2', '2'), ('41', '1', '1', '1', '3', '3'), ('42', '2', '2', '2', '3', '3'), ('43', '3', '3', '3', '3', '3'), ('44', '4', '4', '4', '3', '3'), ('45', '5', '5', '5', '3', '3'), ('46', '6', '6', '6', '3', '3'), ('47', '7', '7', '7', '3', '3'), ('48', '8', '8', '8', '3', '3'), ('49', '9', '9', '9', '3', '3'), ('50', '10', '10', '10', '3', '3'), ('51', '11', '11', '11', '3', '3'), ('52', '12', '12', '12', '3', '3'), ('53', '13', '13', '13', '3', '3'), ('54', '14', '14', '14', '3', '3'), ('55', '15', '15', '15', '3', '3'), ('56', '16', '16', '16', '3', '3'), ('57', '17', '17', '17', '3', '3'), ('58', '18', '18', '18', '3', '3'), ('59', '19', '19', '19', '3', '3'), ('60', '20', '20', '20', '3', '3'), ('61', '1', '1', '1', '4', '0'), ('62', '2', '2', '2', '4', '0'), ('63', '3', '3', '3', '4', '0'), ('64', '4', '4', '4', '4', '0'), ('65', '5', '5', '5', '4', '0'), ('66', '6', '6', '6', '4', '0'), ('67', '7', '7', '7', '4', '0'), ('68', '8', '8', '8', '4', '0'), ('69', '9', '9', '9', '4', '0'), ('70', '10', '10', '10', '4', '0'), ('71', '11', '11', '11', '4', '0'), ('72', '12', '12', '12', '4', '0'), ('73', '13', '13', '13', '4', '0'), ('74', '14', '14', '14', '4', '0'), ('75', '15', '15', '15', '4', '0'), ('76', '16', '16', '16', '4', '0'), ('77', '17', '17', '17', '4', '0'), ('78', '18', '18', '18', '4', '0'), ('79', '19', '19', '19', '4', '0'), ('80', '20', '20', '20', '4', '0'), ('81', '1', '1', '1', '5', '0'), ('82', '2', '2', '2', '5', '0'), ('83', '3', '3', '3', '5', '0'), ('84', '4', '4', '4', '5', '0'), ('85', '5', '5', '5', '5', '0'), ('86', '6', '6', '6', '5', '0'), ('87', '7', '7', '7', '5', '0'), ('88', '8', '8', '8', '5', '0'), ('89', '9', '9', '9', '5', '0'), ('90', '10', '10', '10', '5', '0'), ('91', '11', '11', '11', '5', '0'), ('92', '12', '12', '12', '5', '0'), ('93', '13', '13', '13', '5', '0'), ('94', '14', '14', '14', '5', '0'), ('95', '15', '15', '15', '5', '0'), ('96', '16', '16', '16', '5', '0'), ('97', '17', '17', '17', '5', '0'), ('98', '18', '18', '18', '5', '0'), ('99', '19', '19', '19', '5', '0'), ('100', '20', '20', '20', '5', '0');
COMMIT;

-- ----------------------------
--  Table structure for `qw_score`
-- ----------------------------
DROP TABLE IF EXISTS `qw_score`;
CREATE TABLE `qw_score` (
  `score_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) DEFAULT '0' COMMENT '评价创建者',
  `join_id` int(10) DEFAULT '0' COMMENT '关联id(如景点,目的地等)',
  `order_id` int(10) DEFAULT '0' COMMENT '关联的订单id',
  `score` decimal(10,2) DEFAULT '0.00' COMMENT '评分',
  `score_comment` varchar(255) NOT NULL COMMENT '内容',
  `score_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1景点,2节日，3酒店,4餐厅',
  `score_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0禁用,1启用',
  `score_created_at` int(11) DEFAULT '0',
  `score_updated_at` int(11) DEFAULT '0',
  PRIMARY KEY (`score_id`)
) ENGINE=InnoDB AUTO_INCREMENT=81 DEFAULT CHARSET=utf8mb4 COMMENT='评价表';

-- ----------------------------
--  Records of `qw_score`
-- ----------------------------
BEGIN;
INSERT INTO `qw_score` VALUES ('1', '1', '1', '1', '304.00', '内容0', '1', '1', '1510924337', '1510924337'), ('2', '2', '2', '2', '314.00', '内容1', '1', '1', '1510924337', '1510924337'), ('3', '3', '3', '3', '324.00', '内容2', '1', '1', '1510924337', '1510924337'), ('4', '4', '4', '4', '334.00', '内容3', '1', '1', '1510924337', '1510924337'), ('5', '5', '5', '5', '344.00', '内容4', '1', '1', '1510924337', '1510924337'), ('6', '6', '6', '6', '354.00', '内容5', '1', '1', '1510924337', '1510924337'), ('7', '7', '7', '7', '364.00', '内容6', '1', '1', '1510924337', '1510924337'), ('8', '8', '8', '8', '374.00', '内容7', '1', '1', '1510924337', '1510924337'), ('9', '9', '9', '9', '384.00', '内容8', '1', '1', '1510924337', '1510924337'), ('10', '10', '10', '10', '394.00', '内容9', '1', '1', '1510924337', '1510924337'), ('11', '11', '11', '11', '3104.00', '内容10', '1', '1', '1510924337', '1510924337'), ('12', '12', '12', '12', '3114.00', '内容11', '1', '1', '1510924337', '1510924337'), ('13', '13', '13', '13', '3124.00', '内容12', '1', '1', '1510924337', '1510924337'), ('14', '14', '14', '14', '3134.00', '内容13', '1', '1', '1510924337', '1510924337'), ('15', '15', '15', '15', '3144.00', '内容14', '1', '1', '1510924337', '1510924337'), ('16', '16', '16', '16', '3154.00', '内容15', '1', '1', '1510924337', '1510924337'), ('17', '17', '17', '17', '3164.00', '内容16', '1', '1', '1510924337', '1510924337'), ('18', '18', '18', '18', '3174.00', '内容17', '1', '1', '1510924337', '1510924337'), ('19', '19', '19', '19', '3184.00', '内容18', '1', '1', '1510924337', '1510924337'), ('20', '20', '20', '20', '3194.00', '内容19', '1', '1', '1510924337', '1510924337'), ('21', '1', '1', '1', '304.00', '内容0', '2', '1', '1510924344', '1510924344'), ('22', '2', '2', '2', '314.00', '内容1', '2', '1', '1510924344', '1510924344'), ('23', '3', '3', '3', '324.00', '内容2', '2', '1', '1510924344', '1510924344'), ('24', '4', '4', '4', '334.00', '内容3', '2', '1', '1510924344', '1510924344'), ('25', '5', '5', '5', '344.00', '内容4', '2', '1', '1510924344', '1510924344'), ('26', '6', '6', '6', '354.00', '内容5', '2', '1', '1510924344', '1510924344'), ('27', '7', '7', '7', '364.00', '内容6', '2', '1', '1510924344', '1510924344'), ('28', '8', '8', '8', '374.00', '内容7', '2', '1', '1510924344', '1510924344'), ('29', '9', '9', '9', '384.00', '内容8', '2', '1', '1510924344', '1510924344'), ('30', '10', '10', '10', '394.00', '内容9', '2', '1', '1510924344', '1510924344'), ('31', '11', '11', '11', '3104.00', '内容10', '2', '1', '1510924344', '1510924344'), ('32', '12', '12', '12', '3114.00', '内容11', '2', '1', '1510924344', '1510924344'), ('33', '13', '13', '13', '3124.00', '内容12', '2', '1', '1510924344', '1510924344'), ('34', '14', '14', '14', '3134.00', '内容13', '2', '1', '1510924344', '1510924344'), ('35', '15', '15', '15', '3144.00', '内容14', '2', '1', '1510924344', '1510924344'), ('36', '16', '16', '16', '3154.00', '内容15', '2', '1', '1510924344', '1510924344'), ('37', '17', '17', '17', '3164.00', '内容16', '2', '1', '1510924344', '1510924344'), ('38', '18', '18', '18', '3174.00', '内容17', '2', '1', '1510924344', '1510924344'), ('39', '19', '19', '19', '3184.00', '内容18', '2', '1', '1510924344', '1510924344'), ('40', '20', '20', '20', '3194.00', '内容19', '2', '1', '1510924344', '1510924344'), ('41', '1', '1', '1', '304.00', '内容0', '3', '1', '1510924353', '1510924353'), ('42', '2', '2', '2', '314.00', '内容1', '3', '1', '1510924353', '1510924353'), ('43', '3', '3', '3', '324.00', '内容2', '3', '1', '1510924353', '1510924353'), ('44', '4', '4', '4', '334.00', '内容3', '3', '1', '1510924353', '1510924353'), ('45', '5', '5', '5', '344.00', '内容4', '3', '1', '1510924353', '1510924353'), ('46', '6', '6', '6', '354.00', '内容5', '3', '1', '1510924353', '1510924353'), ('47', '7', '7', '7', '364.00', '内容6', '3', '1', '1510924353', '1510924353'), ('48', '8', '8', '8', '374.00', '内容7', '3', '1', '1510924353', '1510924353'), ('49', '9', '9', '9', '384.00', '内容8', '3', '1', '1510924353', '1510924353'), ('50', '10', '10', '10', '394.00', '内容9', '3', '1', '1510924353', '1510924353'), ('51', '11', '11', '11', '3104.00', '内容10', '3', '1', '1510924353', '1510924353'), ('52', '12', '12', '12', '3114.00', '内容11', '3', '1', '1510924353', '1510924353'), ('53', '13', '13', '13', '3124.00', '内容12', '3', '1', '1510924353', '1510924353'), ('54', '14', '14', '14', '3134.00', '内容13', '3', '1', '1510924353', '1510924353'), ('55', '15', '15', '15', '3144.00', '内容14', '3', '1', '1510924353', '1510924353'), ('56', '16', '16', '16', '3154.00', '内容15', '3', '1', '1510924353', '1510924353'), ('57', '17', '17', '17', '3164.00', '内容16', '3', '1', '1510924353', '1510924353'), ('58', '18', '18', '18', '3174.00', '内容17', '3', '1', '1510924353', '1510924353'), ('59', '19', '19', '19', '3184.00', '内容18', '3', '1', '1510924353', '1510924353'), ('60', '20', '20', '20', '3194.00', '内容19', '3', '1', '1510924353', '1510924353'), ('61', '1', '1', '1', '304.00', '内容0', '4', '1', '1510924359', '1510924359'), ('62', '2', '2', '2', '314.00', '内容1', '4', '1', '1510924359', '1510924359'), ('63', '3', '3', '3', '324.00', '内容2', '4', '1', '1510924359', '1510924359'), ('64', '4', '4', '4', '334.00', '内容3', '4', '1', '1510924359', '1510924359'), ('65', '5', '5', '5', '344.00', '内容4', '4', '1', '1510924359', '1510924359'), ('66', '6', '6', '6', '354.00', '内容5', '4', '1', '1510924359', '1510924359'), ('67', '7', '7', '7', '364.00', '内容6', '4', '1', '1510924359', '1510924359'), ('68', '8', '8', '8', '374.00', '内容7', '4', '1', '1510924359', '1510924359'), ('69', '9', '9', '9', '384.00', '内容8', '4', '1', '1510924359', '1510924359'), ('70', '10', '10', '10', '394.00', '内容9', '4', '1', '1510924359', '1510924359'), ('71', '11', '11', '11', '3104.00', '内容10', '4', '1', '1510924359', '1510924359'), ('72', '12', '12', '12', '3114.00', '内容11', '4', '1', '1510924359', '1510924359'), ('73', '13', '13', '13', '3124.00', '内容12', '4', '1', '1510924359', '1510924359'), ('74', '14', '14', '14', '3134.00', '内容13', '4', '1', '1510924359', '1510924359'), ('75', '15', '15', '15', '3144.00', '内容14', '4', '1', '1510924359', '1510924359'), ('76', '16', '16', '16', '3154.00', '内容15', '4', '1', '1510924359', '1510924359'), ('77', '17', '17', '17', '3164.00', '内容16', '4', '1', '1510924359', '1510924359'), ('78', '18', '18', '18', '3174.00', '内容17', '4', '1', '1510924359', '1510924359'), ('79', '19', '19', '19', '3184.00', '内容18', '4', '1', '1510924359', '1510924359'), ('80', '20', '20', '20', '3194.00', '内容19', '4', '1', '1510924359', '1510924359');
COMMIT;

-- ----------------------------
--  Table structure for `qw_setting`
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
--  Records of `qw_setting`
-- ----------------------------
BEGIN;
INSERT INTO `qw_setting` VALUES ('sitename', '趣玩', '0', '', '0'), ('title', '趣玩后台', '0', '', '0'), ('keywords', '关键词', '0', '', '0'), ('description', '网站描述', '0', '', '0'), ('footer', '2016©趣玩', '0', '', '0'), ('top_banner', '/Public/attached/201609/201609141613191296.png', '1', '顶部banner图片', '1');
COMMIT;

-- ----------------------------
--  Table structure for `qw_shop`
-- ----------------------------
DROP TABLE IF EXISTS `qw_shop`;
CREATE TABLE `qw_shop` (
  `shop_id` int(11) NOT NULL AUTO_INCREMENT,
  `shop_name` varchar(255) DEFAULT NULL COMMENT '商家名称',
  `shop_desc` text COMMENT '商家描述',
  PRIMARY KEY (`shop_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='商家信息';

-- ----------------------------
--  Table structure for `qw_user`
-- ----------------------------
DROP TABLE IF EXISTS `qw_user`;
CREATE TABLE `qw_user` (
  `user_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_nickname` varchar(45) NOT NULL COMMENT '昵称',
  `user_sex` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0女,1男',
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
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COMMENT='用户表';

-- ----------------------------
--  Records of `qw_user`
-- ----------------------------
BEGIN;
INSERT INTO `qw_user` VALUES ('1', '1', '1', 'https://ss1.bdstatic.com/70cFuXSh_Q1YnxGkpoWK1HF6hhy/it/u=547138142,3998729701&fm=27&gp=0.jpg', '1358888881', '1', '1', '20.546566', '10.075546', '', '0', '1', '1510924723', '1510924723'), ('2', '1', '1', 'https://ss1.bdstatic.com/70cFuXSh_Q1YnxGkpoWK1HF6hhy/it/u=547138142,3998729701&fm=27&gp=0.jpg', '1358888882', '1', '1', '21.546566', '11.075546', '', '0', '1', '1510924723', '1510924723'), ('3', '1', '1', 'https://ss1.bdstatic.com/70cFuXSh_Q1YnxGkpoWK1HF6hhy/it/u=547138142,3998729701&fm=27&gp=0.jpg', '1358888883', '1', '1', '22.546566', '12.075546', '', '0', '1', '1510924723', '1510924723'), ('4', '1', '1', 'https://ss1.bdstatic.com/70cFuXSh_Q1YnxGkpoWK1HF6hhy/it/u=547138142,3998729701&fm=27&gp=0.jpg', '1358888884', '1', '1', '23.546566', '13.075546', '', '0', '1', '1510924723', '1510924723'), ('5', '1', '1', 'https://ss1.bdstatic.com/70cFuXSh_Q1YnxGkpoWK1HF6hhy/it/u=547138142,3998729701&fm=27&gp=0.jpg', '1358888885', '1', '1', '24.546566', '14.075546', '', '0', '1', '1510924723', '1510924723'), ('6', '1', '1', 'https://ss1.bdstatic.com/70cFuXSh_Q1YnxGkpoWK1HF6hhy/it/u=547138142,3998729701&fm=27&gp=0.jpg', '1358888886', '1', '1', '25.546566', '15.075546', '', '0', '1', '1510924723', '1510924723'), ('7', '1', '1', 'https://ss1.bdstatic.com/70cFuXSh_Q1YnxGkpoWK1HF6hhy/it/u=547138142,3998729701&fm=27&gp=0.jpg', '1358888887', '1', '1', '26.546566', '16.075546', '', '0', '1', '1510924723', '1510924723'), ('8', '1', '1', 'https://ss1.bdstatic.com/70cFuXSh_Q1YnxGkpoWK1HF6hhy/it/u=547138142,3998729701&fm=27&gp=0.jpg', '1358888888', '1', '1', '27.546566', '17.075546', '', '0', '1', '1510924723', '1510924723'), ('9', '1', '1', 'https://ss1.bdstatic.com/70cFuXSh_Q1YnxGkpoWK1HF6hhy/it/u=547138142,3998729701&fm=27&gp=0.jpg', '1358888889', '1', '1', '28.546566', '18.075546', '', '0', '1', '1510924723', '1510924723'), ('10', '1', '1', 'https://ss1.bdstatic.com/70cFuXSh_Q1YnxGkpoWK1HF6hhy/it/u=547138142,3998729701&fm=27&gp=0.jpg', '1358888890', '1', '1', '29.546566', '19.075546', '', '0', '1', '1510924723', '1510924723'), ('28', '叫我强哥', '1', 'http://wx.qlogo.cn/mmopen/vi_32/Q0j4TwGTfTIfyTSxfzLgsMB0hwGmpe7DsePhIlm6xxuvXn5svoXY9xoGSZoD98tDoq2XHKweeP0juF0naNWblg/0', '', '0', 'ovwAZuBLwSiize3Zjd-DiCZPWTf8', '', '', '', '0', '1', '1511072143', '1511072143');
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;
