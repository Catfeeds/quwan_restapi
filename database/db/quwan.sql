/*
Navicat MySQL Data Transfer

Source Server         : 119.29.87.252
Source Server Version : 50636
Source Host           : 119.29.87.252:3306
Source Database       : quwan

Target Server Type    : MYSQL
Target Server Version : 50636
File Encoding         : 65001

Date: 2017-11-16 20:46:27
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for qw_adv
-- ----------------------------
DROP TABLE IF EXISTS `qw_adv`;
CREATE TABLE `qw_adv` (
  `adv_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(72) NOT NULL COMMENT '标题',
  `url` varchar(255) NOT NULL COMMENT '广告地址',
  `weight` int(11) DEFAULT '0' COMMENT '排序权重',
  `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1首页',
  `img` varchar(255) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0禁用,1启用',
  `created_at` timestamp NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`adv_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for qw_attractions
-- ----------------------------
DROP TABLE IF EXISTS `qw_attractions`;
CREATE TABLE `qw_attractions` (
  `attractions_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL COMMENT '景点名称',
  `address` varchar(255) NOT NULL COMMENT '地址',
  `phone` varchar(32) NOT NULL COMMENT '电话',
  `price` int(10) DEFAULT '0' COMMENT '价格(单位分,使用时候格式化小数)',
  `intro` varchar(255) NOT NULL COMMENT '介绍',
  `score` int(10) DEFAULT '0' COMMENT '评分(单位分,使用时候格式化小数)',
  `evaluation` int(10) DEFAULT '0' COMMENT '评价(单位分,使用时候格式化小数)',
  `is_refund` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否可退货,0否,1是',
  `lon` varchar(64) DEFAULT '' COMMENT '经度',
  `lat` varchar(64) DEFAULT '' COMMENT '纬度',
  `start_at` timestamp NULL DEFAULT '0000-00-00 00:00:00' COMMENT '开放时间',
  `end_at` timestamp NULL DEFAULT '0000-00-00 00:00:00' COMMENT '结束时间',
  `sort` int(10) DEFAULT '0' COMMENT '排序(从小到大)',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0禁用,1启用',
  `created_at` timestamp NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`attractions_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for qw_cid
-- ----------------------------
DROP TABLE IF EXISTS `qw_cid`;
CREATE TABLE `qw_cid` (
  `cid_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL COMMENT '分类名称',
  `type` tinyint(1) DEFAULT '0' COMMENT '分类类型(1景点,2目的地，3路线,4节日，5酒店,6餐厅)',
  `sort` int(10) DEFAULT '0' COMMENT '排序(从小到大)',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0禁用,1启用',
  PRIMARY KEY (`cid_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for qw_cid_map
-- ----------------------------
DROP TABLE IF EXISTS `qw_cid_map`;
CREATE TABLE `qw_cid_map` (
  `cid_map_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `join_id` int(10) DEFAULT '0' COMMENT '关联id(如景点,目的地等)',
  `sort` int(11) DEFAULT '0' COMMENT '排序',
  `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1景点,2目的地，3路线,4节日，5酒店,6餐厅',
  PRIMARY KEY (`cid_map_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for qw_destination
-- ----------------------------
DROP TABLE IF EXISTS `qw_destination`;
CREATE TABLE `qw_destination` (
  `destination_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL COMMENT '名称',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0禁用,1启用',
  `created_at` timestamp NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`destination_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for qw_destination_join
-- ----------------------------
DROP TABLE IF EXISTS `qw_destination_join`;
CREATE TABLE `qw_destination_join` (
  `destination_join_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `destination_id` int(10) DEFAULT '0' COMMENT '目的地id',
  `join_id` int(10) DEFAULT '0' COMMENT '关联id(如景点,目的地等)',
  `sort` int(11) DEFAULT '0' COMMENT '排序',
  `type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1景点,2路线,3酒店,4餐厅',
  PRIMARY KEY (`destination_join_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for qw_fav
-- ----------------------------
DROP TABLE IF EXISTS `qw_fav`;
CREATE TABLE `qw_fav` (
  `fav_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `join_id` int(10) DEFAULT '0' COMMENT '关联id(如景点,目的地等)',
  `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1景点,2节日，3酒店,4餐厅',
  `created_user_id` int(10) DEFAULT '0' COMMENT '创建者',
  `created_at` timestamp NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`fav_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for qw_hall
-- ----------------------------
DROP TABLE IF EXISTS `qw_hall`;
CREATE TABLE `qw_hall` (
  `hall_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL COMMENT '名称',
  `address` varchar(255) NOT NULL COMMENT '地址',
  `phone` varchar(32) NOT NULL COMMENT '电话',
  `price` int(10) DEFAULT '0' COMMENT '价格(单位分,使用时候格式化小数)',
  `intro` varchar(255) NOT NULL COMMENT '介绍',
  `score` int(10) DEFAULT '0' COMMENT '评分(单位分,使用时候格式化小数)',
  `evaluation` int(10) DEFAULT '0' COMMENT '评价(单位分,使用时候格式化小数)',
  `lon` varchar(64) DEFAULT '' COMMENT '经度',
  `lat` varchar(64) DEFAULT '' COMMENT '纬度',
  `start_at` timestamp NULL DEFAULT '0000-00-00 00:00:00' COMMENT '开放时间',
  `end_at` timestamp NULL DEFAULT '0000-00-00 00:00:00' COMMENT '结束时间',
  `sort` int(10) DEFAULT '0' COMMENT '排序(从小到大)',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0禁用,1启用',
  `created_at` timestamp NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`hall_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for qw_holiday
-- ----------------------------
DROP TABLE IF EXISTS `qw_holiday`;
CREATE TABLE `qw_holiday` (
  `holiday_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL COMMENT '名称',
  `address` varchar(255) NOT NULL COMMENT '地址',
  `phone` varchar(32) NOT NULL COMMENT '电话',
  `price` int(10) DEFAULT '0' COMMENT '价格(单位分,使用时候格式化小数)',
  `intro` varchar(255) NOT NULL COMMENT '介绍',
  `score` int(10) DEFAULT '0' COMMENT '评分(单位分,使用时候格式化小数)',
  `evaluation` int(10) DEFAULT '0' COMMENT '评价(单位分,使用时候格式化小数)',
  `lon` varchar(64) DEFAULT '' COMMENT '经度',
  `lat` varchar(64) DEFAULT '' COMMENT '纬度',
  `start_at` timestamp NULL DEFAULT '0000-00-00 00:00:00' COMMENT '开放时间',
  `end_at` timestamp NULL DEFAULT '0000-00-00 00:00:00' COMMENT '结束时间',
  `sort` int(10) DEFAULT '0' COMMENT '排序(从小到大)',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0禁用,1启用',
  `created_at` timestamp NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`holiday_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for qw_home_page
-- ----------------------------
DROP TABLE IF EXISTS `qw_home_page`;
CREATE TABLE `qw_home_page` (
  `home_page_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '页面id',
  `home_page_name` varchar(50) NOT NULL COMMENT '页面名称',
  `home_page_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '首页框框状态，1开启，0关闭',
  `home_page_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '页面类型，1广告，2路线，3目的地，4景点，5节日，6推荐周边',
  `home_page_sort` int(11) NOT NULL DEFAULT '1' COMMENT '排序，小在前',
  PRIMARY KEY (`home_page_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for qw_hotel
-- ----------------------------
DROP TABLE IF EXISTS `qw_hotel`;
CREATE TABLE `qw_hotel` (
  `hotel_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL COMMENT '名称',
  `address` varchar(255) NOT NULL COMMENT '地址',
  `phone` varchar(32) NOT NULL COMMENT '电话',
  `price` int(10) DEFAULT '0' COMMENT '价格(单位分,使用时候格式化小数)',
  `intro` varchar(255) NOT NULL COMMENT '介绍',
  `score` int(10) DEFAULT '0' COMMENT '评分(单位分,使用时候格式化小数)',
  `evaluation` int(10) DEFAULT '0' COMMENT '评价(单位分,使用时候格式化小数)',
  `lon` varchar(64) DEFAULT '' COMMENT '经度',
  `lat` varchar(64) DEFAULT '' COMMENT '纬度',
  `start_at` timestamp NULL DEFAULT '0000-00-00 00:00:00' COMMENT '开放时间',
  `end_at` timestamp NULL DEFAULT '0000-00-00 00:00:00' COMMENT '结束时间',
  `sort` int(10) DEFAULT '0' COMMENT '排序(从小到大)',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0禁用,1启用',
  `created_at` timestamp NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`hotel_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for qw_img
-- ----------------------------
DROP TABLE IF EXISTS `qw_img`;
CREATE TABLE `qw_img` (
  `img_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `join_id` int(10) DEFAULT '0' COMMENT '关联id(如景点,目的地等)',
  `sort` int(11) DEFAULT '0' COMMENT '排序',
  `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1景点,2目的地，3路线,4节日，5酒店,6餐厅,7评价',
  `img` varchar(255) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0禁用,1启用',
  `created_at` timestamp NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`img_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for qw_message
-- ----------------------------
DROP TABLE IF EXISTS `qw_message`;
CREATE TABLE `qw_message` (
  `message_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `to_user_id` int(10) DEFAULT '0' COMMENT '1全部用户,2单个用户',
  `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1全部用户,2单个用户',
  `read` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0未读1已读',
  `comment` varchar(255) NOT NULL COMMENT '内容',
  `created_user_id` int(10) DEFAULT '0' COMMENT '创建者',
  `created_at` timestamp NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`message_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for qw_order
-- ----------------------------
DROP TABLE IF EXISTS `qw_order`;
CREATE TABLE `qw_order` (
  `order_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_sn` varchar(50) NOT NULL COMMENT '订单号',
  `join_id` int(10) DEFAULT '0' COMMENT '商品关联id(如景点,目的地等)',
  `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '订单类型1景点,2节日，3酒店,4餐厅',
  `num` int(10) DEFAULT '0' COMMENT '商品数量',
  `price` int(10) DEFAULT '0' COMMENT '商品单价(单位分,使用时候格式化小数)',
  `amount` int(10) DEFAULT '0' COMMENT '商品总价(单位分,使用时候格式化小数)',
  `pay_amount` int(10) DEFAULT '0' COMMENT '订单实付款(单位分,使用时候格式化小数)',
  `refund_amount` int(10) DEFAULT '0' COMMENT '退款金额(单位分,使用时候格式化小数)',
  `reward_amount` int(10) DEFAULT '0' COMMENT '奖励的红包金额(单位分,使用时候格式化小数)',
  `code` varchar(64) NOT NULL COMMENT '兑换码',
  `is_exchange` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否兑换核销,0未,1已',
  `is_score` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否评价,0未,1已',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '订单状态(1未付款,2可使用,3待评价,4已完成,5已取消)',
  `created_user_id` int(10) DEFAULT '0' COMMENT '创建者',
  `pay_at` timestamp NULL DEFAULT '0000-00-00 00:00:00' COMMENT '支付时间',
  `refund_at` timestamp NULL DEFAULT '0000-00-00 00:00:00' COMMENT '退款时间',
  `cancel_at` timestamp NULL DEFAULT '0000-00-00 00:00:00' COMMENT '取消时间',
  `created_at` timestamp NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for qw_route
-- ----------------------------
DROP TABLE IF EXISTS `qw_route`;
CREATE TABLE `qw_route` (
  `route_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL COMMENT '名称',
  `day_num` tinyint(1) NOT NULL DEFAULT '1' COMMENT '天数',
  `created_user_id` int(10) DEFAULT '0' COMMENT '线路创建者',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0禁用,1启用',
  `created_at` timestamp NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`route_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for qw_route_day
-- ----------------------------
DROP TABLE IF EXISTS `qw_route_day`;
CREATE TABLE `qw_route_day` (
  `route_day_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `route_id` int(10) DEFAULT '0' COMMENT '目的地id',
  `intro` varchar(255) NOT NULL COMMENT '日程介绍',
  PRIMARY KEY (`route_day_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for qw_route_day_join
-- ----------------------------
DROP TABLE IF EXISTS `qw_route_day_join`;
CREATE TABLE `qw_route_day_join` (
  `route_day_join_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `route_day_id` int(10) DEFAULT '0' COMMENT '线路日程id',
  `join_id` int(10) DEFAULT '0' COMMENT '关联id(如景点,目的地等)',
  `sort` int(11) DEFAULT '0' COMMENT '排序',
  `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1景点,2目的地，3路线,4节日，5酒店,6餐厅',
  PRIMARY KEY (`route_day_join_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for qw_score
-- ----------------------------
DROP TABLE IF EXISTS `qw_score`;
CREATE TABLE `qw_score` (
  `score_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `created_user_id` int(10) DEFAULT '0' COMMENT '评价创建者',
  `join_id` int(10) DEFAULT '0' COMMENT '关联id(如景点,目的地等)',
  `order_id` int(10) DEFAULT '0' COMMENT '关联的订单id',
  `score` int(10) DEFAULT '0' COMMENT '评分(单位分,使用时候格式化小数)',
  `comment` varchar(255) NOT NULL COMMENT '内容',
  `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1景点,2路线,3节日，4酒店,5餐厅',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0禁用,1启用',
  `created_at` timestamp NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`score_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for qw_user
-- ----------------------------
DROP TABLE IF EXISTS `qw_user`;
CREATE TABLE `qw_user` (
  `user_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nickname` varchar(45) NOT NULL COMMENT '昵称',
  `sex` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0女,1男',
  `avatar` varchar(255) NOT NULL COMMENT '头像',
  `mobile` varchar(32) NOT NULL DEFAULT '' COMMENT '手机号码',
  `is_binding` tinyint(1) NOT NULL DEFAULT '1' COMMENT '手机绑定(0未绑定,1已绑定)',
  `openid` varchar(62) NOT NULL DEFAULT '' COMMENT '微信openid',
  `lon` varchar(64) DEFAULT '' COMMENT '经度',
  `lat` varchar(64) DEFAULT '' COMMENT '纬度',
  `msg_num` int(10) unsigned DEFAULT '0' COMMENT '未读消息数(后台发一条+1,用户读一条-1)',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0禁用,1启用',
  `created_at` timestamp NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=COMPACT;
