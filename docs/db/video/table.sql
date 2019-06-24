-- phpMyAdmin SQL Dump
-- version phpStudy 2014
-- http://www.phpmyadmin.net
--
-- 主机: localhost
-- 生成日期: 2019 年 06 月 24 日 23:10
-- 服务器版本: 5.5.53
-- PHP 版本: 5.4.45

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- 数据库: `video`
--

-- --------------------------------------------------------

--
-- 表的结构 `hls_m3u8`
--

CREATE TABLE IF NOT EXISTS `hls_m3u8` (
  `m3u8_id` int(11) NOT NULL AUTO_INCREMENT,
  `url` text COLLATE utf8mb4_unicode_ci,
  `title` text COLLATE utf8mb4_unicode_ci,
  `name` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `playlist` int(10) DEFAULT '0',
  `list_no` int(10) DEFAULT '-1',
  `note` text COLLATE utf8mb4_unicode_ci,
  `created` int(10) DEFAULT NULL,
  `modified` int(10) DEFAULT NULL,
  `updated` int(10) DEFAULT NULL,
  `updates` int(10) DEFAULT NULL,
  `status` int(10) DEFAULT '0' COMMENT '-1隐藏0未定义1可搜索2首页',
  PRIMARY KEY (`m3u8_id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=159 ;
