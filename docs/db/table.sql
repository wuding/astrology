-- Adminer 4.6.3 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

DROP TABLE IF EXISTS `rental_method`;
CREATE TABLE `rental_method` (
  `method_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '租赁方式ID',
  `title` varchar(512) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '租赁方式名称',
  `updated` int(10) DEFAULT '0' COMMENT '更新时间',
  `created` int(10) DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`method_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='租赁方式';


DROP TABLE IF EXISTS `renting_site_area`;
CREATE TABLE `renting_site_area` (
  `area_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '地区ID',
  `upper_id` int(11) DEFAULT '0' COMMENT '上级ID',
  `site_id` int(11) DEFAULT '0' COMMENT '站点ID',
  `title` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '中文名',
  `name` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '拼音或英文',
  `abbr` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '缩写',
  `origin_id` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '原始ID',
  `type` tinyint(4) DEFAULT '0' COMMENT '类型 0未知 1省 2市 3区 4镇 5村 6小区',
  `status` tinyint(4) DEFAULT '0' COMMENT '状态 -1删除 0未知 1正常',
  `note` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `created` int(11) DEFAULT '0' COMMENT '创建时间戳',
  `updated` int(11) DEFAULT '0' COMMENT '更新时间戳',
  PRIMARY KEY (`area_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='租房站点地区';


DROP TABLE IF EXISTS `renting_site_detail`;
CREATE TABLE `renting_site_detail` (
  `detail_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '详情ID',
  `site_id` int(10) DEFAULT '0' COMMENT '站点ID',
  `city_name` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '城市',
  `agent_id` int(10) DEFAULT '0' COMMENT '经纪人ID',
  `item_id` int(10) DEFAULT '0' COMMENT '原始ID',
  `title` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '标题',
  `description` varchar(2048) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '描述',
  `pic` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '图片',
  `slides` longtext COLLATE utf8mb4_unicode_ci COMMENT '相册幻灯',
  `type` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '类型',
  `data` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '附加数据',
  `rental_price` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '租金',
  `pay` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '支付方式',
  `refresh_time` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '刷新时间',
  `tags` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '标签',
  `facilities` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '配套设施',
  `house_type` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '户型',
  `building_area` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '建筑面积',
  `floor` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '楼层',
  `total_floor` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '总楼层',
  `orientation` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '朝向',
  `decoration` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '装修',
  `check_in_time` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '入住时间',
  `rental_method` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '租赁方式',
  `district_name` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '区县',
  `area_id` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '地区ID',
  `complex_id` bigint(20) DEFAULT '0' COMMENT '小区ID',
  `complex_name` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '小区',
  `note` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `status` int(10) DEFAULT '0' COMMENT '状态 0未知 -1新建 -2更新 -3解析',
  `cache_set` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '缓存要更新的内容',
  `created` int(10) DEFAULT '0' COMMENT '创建时间戳',
  `updated` int(10) DEFAULT '0' COMMENT '更新时间戳',
  PRIMARY KEY (`detail_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='租房站点详情';


DROP TABLE IF EXISTS `rent_house_type`;
CREATE TABLE `rent_house_type` (
  `type_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '户型ID',
  `title` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '户型名',
  `updated` int(10) DEFAULT '0' COMMENT '更新时间',
  `created` int(10) DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`type_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='租房户型';


DROP TABLE IF EXISTS `rent_list`;
CREATE TABLE `rent_list` (
  `list_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '租房列表ID',
  `detail_id` int(10) DEFAULT '-1' COMMENT '详情标识',
  `province_id` int(10) DEFAULT '-1' COMMENT '省市ID',
  `city_id` int(10) DEFAULT '-1' COMMENT '城市ID',
  `district_id` int(10) DEFAULT '-1' COMMENT '区县ID',
  `town_id` int(10) DEFAULT '-1' COMMENT '乡镇ID',
  `village_id` int(10) DEFAULT '-1' COMMENT '村ID',
  `complex_id` int(10) DEFAULT '-1' COMMENT '小区ID',
  `rental_method` int(3) DEFAULT '-1' COMMENT '出租方式',
  `rental_price` int(9) DEFAULT '-1' COMMENT '租金',
  `house_type` int(3) DEFAULT '-1' COMMENT '户型',
  `building_area` int(5) DEFAULT '-1' COMMENT '建筑面积',
  `title` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '标题',
  `pic` varchar(2048) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '图片',
  `tags` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '标签',
  `synchronized` int(10) DEFAULT '0' COMMENT '同步时间',
  `updated` int(10) DEFAULT '0' COMMENT '更新时间',
  `created` int(10) DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`list_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='出租列表';


DROP TABLE IF EXISTS `rent_tag`;
CREATE TABLE `rent_tag` (
  `tag_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '标签ID',
  `title` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '标签名',
  `updated` int(10) DEFAULT '0' COMMENT '更新时间',
  `created` int(10) DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`tag_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='租房亮点标签';


-- 2019-01-17 20:09:32