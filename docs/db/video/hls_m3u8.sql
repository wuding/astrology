-- phpMyAdmin SQL Dump
-- version phpStudy 2014
-- http://www.phpmyadmin.net
--
-- 主机: localhost
-- 生成日期: 2019 年 05 月 13 日 19:26
-- 服务器版本: 5.5.53
-- PHP 版本: 5.4.45

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

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
  PRIMARY KEY (`m3u8_id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=146 ;

--
-- 转存表中的数据 `hls_m3u8`
--

INSERT INTO `hls_m3u8` (`m3u8_id`, `url`, `title`, `name`, `playlist`, `list_no`, `note`, `created`, `modified`, `updated`, `updates`) VALUES
(1, 'https://v-xunlei.com/20180803/6527_bfc11a36/index.m3u8', '香蜜沉沉烬如霜 第1集', 'xmccjrs1', 1, 1, NULL, NULL, NULL, NULL, NULL),
(2, 'https://v-xunlei.com/20180803/6526_1f1979e9/index.m3u8', '香蜜沉沉烬如霜 第2集', 'xmccjrs2', 1, 2, NULL, NULL, NULL, NULL, NULL),
(3, 'https://v-xunlei.com/20180803/6562_abf9974d/index.m3u8', '香蜜沉沉烬如霜 第3集', 'xmccjrs3', 1, 3, NULL, NULL, NULL, NULL, NULL),
(4, 'https://v-xunlei.com/20180803/6559_3b60f76d/index.m3u8', '香蜜沉沉烬如霜 第4集', 'xmccjrs4', 1, 4, NULL, NULL, NULL, NULL, NULL),
(5, 'https://v-xunlei.com/20180804/6613_a909360f/index.m3u8', '香蜜沉沉烬如霜 第5集', 'xmccjrs5', 1, 5, NULL, NULL, NULL, NULL, NULL),
(6, 'https://v-xunlei.com/20180805/6694_34971888/index.m3u8', '香蜜沉沉烬如霜 第6集', 'xmccjrs6', 1, 6, NULL, NULL, NULL, NULL, NULL),
(7, 'https://v-xunlei.com/20180805/6693_095a7b0a/index.m3u8', '香蜜沉沉烬如霜 第7集', 'xmccjrs7', 1, 7, NULL, NULL, NULL, NULL, NULL),
(8, 'https://v-xunlei.com/20180806/6765_8c0b3b28/index.m3u8', '香蜜沉沉烬如霜 第8集', 'xmccjrs8', 1, 8, NULL, NULL, NULL, NULL, NULL),
(9, 'https://v-xunlei.com/20180806/6764_63a7bb8e/index.m3u8', '香蜜沉沉烬如霜 第9集', 'xmccjrs9', 1, 9, NULL, NULL, NULL, NULL, NULL),
(10, 'https://v-xunlei.com/20180807/6825_ec095e73/index.m3u8', '香蜜沉沉烬如霜 第10集', 'xmccjrs10', 1, 10, NULL, NULL, NULL, NULL, NULL),
(11, 'https://v-xunlei.com/20180807/6824_1b0a7cf7/index.m3u8', '香蜜沉沉烬如霜 第11集', 'xmccjrs11', 1, 11, NULL, NULL, NULL, NULL, NULL),
(12, 'https://v-xunlei.com/20180808/6904_633d279c/index.m3u8', '香蜜沉沉烬如霜 第12集', 'xmccjrs12', 1, 12, NULL, NULL, NULL, NULL, NULL),
(13, 'https://v-xunlei.com/20180808/6903_17b6ece6/index.m3u8', '香蜜沉沉烬如霜 第13集', 'xmccjrs13', 1, 13, NULL, NULL, NULL, NULL, NULL),
(14, 'https://v-xunlei.com/20180809/6949_ccf084f4/index.m3u8', '香蜜沉沉烬如霜 第14集', 'xmccjrs14', 1, 14, NULL, NULL, NULL, NULL, NULL),
(15, 'https://v-xunlei.com/20180809/6948_ee5f7d34/index.m3u8', '香蜜沉沉烬如霜 第15集', 'xmccjrs15', 1, 15, NULL, NULL, NULL, NULL, NULL),
(16, 'https://v-xunlei.com/20180810/6989_42f1e2ca/index.m3u8', '香蜜沉沉烬如霜 第16集', 'xmccjrs16', 1, 16, NULL, NULL, NULL, NULL, NULL),
(17, 'https://v-xunlei.com/20180810/6988_70de02d7/index.m3u8', '香蜜沉沉烬如霜 第17集', 'xmccjrs17', 1, 17, NULL, NULL, NULL, NULL, NULL),
(18, 'https://v-xunlei.com/20180811/7095_cd3763ff/index.m3u8', '香蜜沉沉烬如霜 第18集', 'xmccjrs18', 1, 18, NULL, NULL, NULL, NULL, NULL),
(19, 'https://v-xunlei.com/20180812/7155_8f9dc2b1/index.m3u8', '香蜜沉沉烬如霜 第19集', 'xmccjrs19', 1, 19, NULL, NULL, NULL, NULL, NULL),
(20, 'https://v-xunlei.com/20180812/7154_5eef5d44/index.m3u8', '香蜜沉沉烬如霜 第20集', 'xmccjrs20', 1, 20, NULL, NULL, NULL, NULL, NULL),
(21, 'https://v-xunlei.com/20180813/7199_b7eacc76/index.m3u8', '香蜜沉沉烬如霜 第21集', 'xmccjrs21', 1, 21, NULL, NULL, NULL, NULL, NULL),
(22, 'https://v-xunlei.com/20180813/7198_5e7bcb94/index.m3u8', '香蜜沉沉烬如霜 第22集', 'xmccjrs22', 1, 22, NULL, NULL, NULL, NULL, NULL),
(23, 'https://yong.yongjiu6.com/20180814/7BHbKPlj/index.m3u8', '香蜜沉沉烬如霜 第23集', 'xmccjrs23', 1, 23, NULL, NULL, NULL, NULL, NULL),
(24, 'https://yong.yongjiu6.com/20180814/Whh4vw1g/index.m3u8', '香蜜沉沉烬如霜 第24集', 'xmccjrs24', 1, 24, NULL, NULL, NULL, NULL, NULL),
(25, 'http://yong.yongjiu6.com/20180918/X0qET9Ne/index.m3u8', '精灵旅社3：疯狂假期', 'jlls3', 0, -1, 'https://yong.yongjiu6.com/20180730/a0EpKQmn/index.m3u8', NULL, NULL, NULL, NULL),
(26, 'https://135zyv5.xw0371.com/2018/08/30/ODxUA7NNOuoKdlaV/playlist.m3u8', '狄仁杰之四大天王', 'drjzsdtw', 0, -1, 'https://v8.yongjiu8.com/20180731/rkOe3jjn/index.m3u8', NULL, NULL, NULL, NULL),
(27, 'https://v8.yongjiu8.com/20180730/d3XWhZrA/index.m3u8', '西虹市首富', 'xhssf', 0, -1, NULL, NULL, NULL, NULL, NULL),
(28, 'https://v8.yongjiu8.com/20180801/k5JHNg2q/index.m3u8', '复仇者联盟3：无限战争', 'fczlm3', 0, -1, NULL, NULL, NULL, NULL, NULL),
(29, 'https://cdn.zypll.com/20180801/StYVMohv/index.m3u8', 'pottygirl写真253', 'pottygirl253', 0, -1, NULL, NULL, NULL, NULL, NULL),
(30, 'https://vip.kuyun99.com/20180811/HRxpCdKe/index.m3u8', '死侍2', 'deadpool2', 0, -1, NULL, NULL, NULL, NULL, NULL),
(31, 'https://zuikzy.51moca.com/2018/11/25/KJg67dqoefT9Qm5w/playlist.m3u8', '一出好戏', 'ychx', 0, -1, 'https://yong.yongjiu6.com/20180811/wslGYCAN/index.m3u8', NULL, NULL, NULL, NULL),
(32, 'http://yong.yongjiu6.com/20180918/9BcqY33k/index.m3u8', '风语咒', 'fyz', 0, -1, 'https://yong.yongjiu6.com/20180811/ISogqRX3/index.m3u8\r\nhttp://yong.yongjiu6.com/20180916/Qxyl7owK/index.m3u8', NULL, NULL, NULL, NULL),
(33, 'https://v4.438vip.com/20180814/UspEWQPQ/index.m3u8', '爱情公寓 抢先版', 'aqgy', 0, -1, 'https://cdn.kuyunbo.club/20180811/VbrkTv1d/index.m3u8', NULL, NULL, NULL, NULL),
(34, 'https://bobo.kukucdn.com/20180814/b0i9wvKD/index.m3u8', '爱情进化论 第24集', 'aqjhl24', 2, 24, NULL, NULL, NULL, NULL, NULL),
(35, 'https://bobo.kukucdn.com/20180814/kNy4ZcoJ/index.m3u8', '爱情进化论 第23集', 'aqjhl23', 2, 23, NULL, NULL, NULL, NULL, NULL),
(36, 'https://135zyv5.xw0371.com/2018/09/13/k12E0hiIrwEgQAiE/playlist.m3u8', '碟中谍6：全面瓦解 高清', 'dzd6', 0, -1, 'https://boba.52kuyun.com/20180815/5QncdKAe/index.m3u8', NULL, NULL, NULL, NULL),
(37, 'https://yong.yongjiu6.com/20180814/5EtmHHmb/index.m3u8', '巨齿鲨', 'themag', 0, -1, NULL, NULL, NULL, NULL, NULL),
(38, 'https://yong.yongjiu6.com/20180814/NEhCszRk/index.m3u8', '小偷家族', 'shoplifters', 0, -1, NULL, NULL, NULL, NULL, NULL),
(39, 'https://135zyv4.xw0371.com/2018/08/14/7s71mN4FNjGMwqb4/playlist.m3u8', '夜天子 第1集', 'ytz1', 3, 1, NULL, NULL, NULL, NULL, NULL),
(40, 'https://135zyv4.xw0371.com/2018/08/14/BYnoC97lgIuYB4Vz/playlist.m3u8', '夜天子 第2集', 'ytz2', 3, 2, NULL, NULL, NULL, NULL, NULL),
(41, 'https://135zyv4.xw0371.com/2018/08/14/ZvqrSPOfa1zp128Q/playlist.m3u8', '夜天子 第3集', 'ytz3', 3, 3, NULL, NULL, NULL, NULL, NULL),
(42, 'https://135zyv4.xw0371.com/2018/08/14/O1KmF4X10bOrtXbE/playlist.m3u8', '夜天子 第4集', 'ytz4', 3, 4, NULL, NULL, NULL, NULL, NULL),
(43, 'https://135zyv4.xw0371.com/2018/08/14/m1bx5HtpJy1YHO7e/playlist.m3u8', '夜天子 第5集', 'ytz5', 3, 5, NULL, NULL, NULL, NULL, NULL),
(44, 'https://135zyv4.xw0371.com/2018/08/14/yqywgOJolxIPGfHz/playlist.m3u8', '夜天子 第6集', 'ytz6', 3, 6, NULL, NULL, NULL, NULL, NULL),
(45, 'https://135zyv4.xw0371.com/2018/08/14/5TDXI0CtBH0aQd69/playlist.m3u8', '夜天子 第7集', 'ytz7', 3, 7, NULL, NULL, NULL, NULL, NULL),
(46, 'https://135zyv4.xw0371.com/2018/08/14/ZCz8hI1qrfGZIfHl/playlist.m3u8', '夜天子 第8集', 'ytz8', 3, 8, NULL, NULL, NULL, NULL, NULL),
(47, 'https://135zyv4.xw0371.com/2018/08/14/VQxqwYuHsopSsbBN/playlist.m3u8', '夜天子 第9集', 'ytz9', 3, 9, NULL, NULL, NULL, NULL, NULL),
(48, 'https://135zyv4.xw0371.com/2018/08/14/UK8Cyxute2bf1UJv/playlist.m3u8', '夜天子 第10集', 'ytz10', 3, 10, NULL, NULL, NULL, NULL, NULL),
(49, 'https://135zyv4.xw0371.com/2018/08/14/ouEqBXECFzDsZAZP/playlist.m3u8', '夜天子 第11集', 'ytz11', 3, 11, NULL, NULL, NULL, NULL, NULL),
(50, 'https://135zyv4.xw0371.com/2018/08/14/Y8ImkNZXY4cSQ38l/playlist.m3u8', '夜天子 第12集', 'ytz12', 3, 12, NULL, NULL, NULL, NULL, NULL),
(51, 'http://yong.yongjiu6.com/20180815/wv808zaC/index.m3u8', '香蜜沉沉烬如霜 第25集', 'xmccjrs25', 1, 25, NULL, NULL, NULL, NULL, NULL),
(52, 'http://yong.yongjiu6.com/20180815/CWA8dhQx/index.m3u8', '香蜜沉沉烬如霜 第26集', 'xmccjrs26', 1, 26, NULL, NULL, NULL, NULL, NULL),
(53, 'https://135zyv4.xw0371.com/2018/08/16/Gcd2QqW2HxJSSjuT/playlist.m3u8', '香蜜沉沉烬如霜 第27集', 'xmccjrs27', 1, 27, NULL, NULL, NULL, NULL, NULL),
(54, 'https://135zyv4.xw0371.com/2018/08/16/KLV7P387mf6vBCMq/playlist.m3u8', '香蜜沉沉烬如霜 第28集', 'xmccjrs28', 1, 28, NULL, NULL, NULL, NULL, NULL),
(55, 'https://135zyv4.xw0371.com/2018/08/17/DyA3TRfHwkqxs5qz/playlist.m3u8', '香蜜沉沉烬如霜 第29集', 'xmccjrs29', 1, 29, NULL, NULL, NULL, NULL, NULL),
(56, 'https://135zyv4.xw0371.com/2018/08/17/j86RP5lH1WrsLnCM/playlist.m3u8', '香蜜沉沉烬如霜 第30集', 'xmccjsr30', 1, 30, NULL, NULL, NULL, NULL, NULL),
(57, 'https://135zyv4.xw0371.com/2018/08/16/PmDHRQf1ae4mkqTV/playlist.m3u8', '龙门飞甲 第1集', 'lmfj1', 4, 1, NULL, NULL, NULL, NULL, NULL),
(58, 'https://135zyv5.xw0371.com/2018/09/09/wQbJQqMBglvc13Bo/playlist.m3u8', '新乌龙院之笑闹江湖', 'xwly', 0, -1, 'https://52dy.hanju2017.com/20180818/U8SkzcAv/index.m3u8', NULL, NULL, NULL, NULL),
(59, 'https://135zyv4.xw0371.com/2018/08/18/40Dp1LDQCVyXjbTI/playlist.m3u8', '香蜜沉沉烬如霜 第31集', 'xmccjrs31', 1, 31, NULL, NULL, NULL, NULL, NULL),
(60, 'https://135zyv5.xw0371.com/2018/09/14/whGXAJfnucq6u5OT/playlist.m3u8', '欧洲攻略 高清', 'ozgl', 0, -1, 'https://v4.438vip.com/20180818/juRFi53a/index.m3u8\r\nhttps://cdn.zypbo.com/20180827/82XIHnoS/index.m3u8\r\nhttps://v-6-cn.com/20180828/6197_d8346637/index.m3u8', NULL, NULL, NULL, NULL),
(61, 'https://boba.52kuyun.com/20180829/ur61Hjam/index.m3u8', '快把我哥带走 高清', 'kbgdz', 0, -1, NULL, NULL, NULL, NULL, NULL),
(62, 'https://v4.438vip.com/20180815/xtsgrZiA/index.m3u8', '拯救悟空', 'zjwk', 0, -1, NULL, NULL, NULL, NULL, NULL),
(63, 'https://v-xunlei.com/20180820/7627_b50ac4e3/index.m3u8', '如懿传 第1集', 'ryz1', 5, 1, 'https://boba.52kuyun.com/20180820/BAjTZuKx/index.m3u8', NULL, NULL, NULL, NULL),
(64, 'https://135zyv6.xw0371.com/2018/11/23/BRQ9RRgMeAi1bCPj/playlist.m3u8', '邪不压正 高清', 'xbyz', 0, -1, 'https://v4.438vip.com/20180823/azWVhOZ9/index.m3u8', NULL, NULL, NULL, NULL),
(65, 'https://vip.kuyun99.com/20180828/3qBPolSZ/index.m3u8', '大师兄 高清', 'dsx', 0, -1, NULL, NULL, NULL, NULL, NULL),
(66, 'http://yong.yongjiu6.com/20180828/kwd8v4BF/index.m3u8', '蚁人2：黄蜂女现身', 'yr2', 0, -1, NULL, NULL, NULL, NULL, NULL),
(67, 'https://135zyv5.xw0371.com/2018/09/10/FlgUfGZNassISVA9/playlist.m3u8', '游侠索罗：星球大战外传', 'solo', 0, -1, NULL, NULL, NULL, NULL, NULL),
(68, 'https://52dy.hanju2017.com/20181230/16213_569ffe9b/index.m3u8', '铁血战士2018 国语', 'txzs2018', 0, -1, 'https://boba.52kuyun.com/20180915/MrTFpJRy/index.m3u8\r\n\r\nhttps://135zyv6.xw0371.com/2018/11/28/P33t6HOEex44uV8V/playlist.m3u8', NULL, NULL, NULL, NULL),
(69, 'https://135zyv5.xw0371.com/2018/10/23/0QzXKrMtU3tjZTC0/playlist.m3u8', '超人总动员2', 'Incredibles2', 0, -1, NULL, NULL, NULL, NULL, NULL),
(70, 'https://135zyv5.xw0371.com/2018/09/27/aO6WXoI5Kgucp7mo/playlist.m3u8', '动物世界', 'AnimalWorld', 0, -1, NULL, NULL, NULL, NULL, NULL),
(71, 'https://bobo.kukucdn.com/20190109/3712_c8ae6a3d/index.m3u8', '毒液：致命守护者', 'Venom', 0, -1, 'https://135zyv6.xw0371.com/2018/11/23/dpnsuPmseYlh5QT2/playlist.m3u8', NULL, NULL, NULL, NULL),
(72, 'http://bobo.zuixin-bo.com/20180226/C6Q7Erkj/index.m3u8', '正义联盟', 'JusticeLeague', 0, -1, NULL, NULL, NULL, NULL, NULL),
(73, 'https://135zyv6.xw0371.com/2018/11/28/XgKvOt3TrqcjjsSs/playlist.m3u8', '功夫联盟', 'gflm', 0, -1, NULL, NULL, NULL, NULL, NULL),
(74, 'https://135zyv6.xw0371.com/2018/12/04/5CgxUQ8bC35v7bQ0/playlist.m3u8', '冒牌搭档', 'mpdd', 0, -1, 'https://v6.438vip.com/2018/12/01/DemJ6jtCAmE6pDLB/playlist.m3u8', NULL, NULL, NULL, NULL),
(75, 'https://bobo.kukucdn.com/20181130/1778_de9b8e3f/index.m3u8', '影', 'Shadow', 0, -1, NULL, NULL, NULL, NULL, NULL),
(76, 'https://135zyv6.xw0371.com/2018/12/08/sBcLQb1sQuMPqCmO/playlist.m3u8', '雪怪大冒险', 'Smallfoot', 0, -1, 'https://cdn.youku-letv.com/20181201/11226_004a7598/index.m3u8', NULL, NULL, NULL, NULL),
(77, 'https://v6.165zy.com//20181202/MWA6OpeY/index.m3u8', '贝利：传奇的诞生', 'Pele', 0, -1, NULL, NULL, NULL, NULL, NULL),
(78, 'https://boba.52kuyun.com/20190102/18434_59fec7fc/index.m3u8', '无名之辈', 'wmzb', 0, -1, 'https://135zyv6.xw0371.com/2018/12/02/8K6Y7ZejR4DJLYJh/playlist.m3u8', NULL, NULL, NULL, NULL),
(79, 'https://cdn.youku-letv.com/20181205/11539_69f10a03/index.m3u8', '印度合伙人 护垫侠', 'padman', 0, -1, NULL, NULL, NULL, NULL, NULL),
(80, 'https://135zyv5.xw0371.com/2018/08/21/efXTAj5oVREbONlS/playlist.m3u8', '惊涛飓浪', 'Adrift', 0, -1, NULL, NULL, NULL, NULL, NULL),
(81, 'https://boba.52kuyun.com/20181213/18196_d158bb29/index.m3u8', '蜘蛛侠：平行宇宙 抢鲜版', 'SpiderMan2018', 0, -1, NULL, NULL, NULL, NULL, NULL),
(82, 'https://cdn.kuyunbo.club/20181213/12823_0a69b907/index.m3u8', '李茶的姑妈', 'lcdgm', 0, -1, NULL, NULL, NULL, NULL, NULL),
(83, 'http://sohu.zuida-163sina.com/20190209/chvSA9fA/index.m3u8', '海王 高清', 'Aquaman', 0, -1, 'https://bobo.kukucdn.com/20181217/2519_150afd59/index.m3u8', NULL, NULL, NULL, NULL),
(84, 'https://bobo.kukucdn.com/20181218/2559_74ecab36/index.m3u8', '黄飞鸿之怒海雄风', 'hfhznhxf', 0, -1, NULL, NULL, NULL, NULL, NULL),
(85, 'https://135zyv6.xw0371.com/2019/01/24/161kw2QpGO9z9AAi/playlist.m3u8', '天气预爆 高清', 'Airpocalypse', 0, -1, 'https://135zyv6.xw0371.com/2018/12/21/9xF7TkPZ82GH9thE/playlist.m3u8', NULL, NULL, NULL, NULL),
(86, 'https://135zyv6.xw0371.com/2019/01/31/2h98xoBXcrPhSR22/playlist.m3u8', '武林怪兽 高清', 'wlgs', 0, -1, 'https://135zyv6.xw0371.com/2018/12/22/8ajLx30IIzS3103J/playlist.m3u8', NULL, NULL, NULL, NULL),
(87, 'https://135zyv6.xw0371.com/2019/02/07/xOnYwMvoKjT6jpeh/playlist.m3u8', '叶问外传：张天志 高清', 'ztz', 0, -1, 'https://52dy.hanju2017.com/20181221/15811_f7353e3c/index.m3u8', NULL, NULL, NULL, NULL),
(88, 'https://bobo.kukucdn.com/20181230/3126_56467b01/index.m3u8', '冰封侠：时空行者', 'bfx2', 0, -1, NULL, NULL, NULL, NULL, NULL),
(89, 'https://52dy.hanju2017.com/20190208/18031_b86e33a0/index.m3u8', '断片之险途夺宝 高清', 'dp', 0, -1, NULL, NULL, NULL, NULL, NULL),
(90, 'https://135zyv6.xw0371.com/2019/02/12/BaSyUVp9oFbpI2MZ/playlist.m3u8', '无敌破坏王2：大闹互联网', 'wdphw2', 0, -1, 'https://cdn.youku-letv.com/20181230/13673_e8173fe7/index.m3u8\r\n\r\nhttps://boba.52kuyun.com/20190102/18430_4309a482/index.m3u8', NULL, NULL, NULL, NULL),
(91, 'https://cdn.youku-letv.com/20181230/13663_4b47bab8/index.m3u8', '斯大林格勒', 'Stalingrad', 0, -1, NULL, NULL, NULL, NULL, NULL),
(92, 'https://52dy.hanju2017.com/20190101/16243_aa6675ea/index.m3u8', '泰勒·斯威夫特：“举世盛名”巡回演唱会', 'ts', 0, -1, NULL, NULL, NULL, NULL, NULL),
(93, 'https://52dy.hanju2017.com/20190101/16241_ff1b446f/index.m3u8', '神奇女侠', 'WonderWoman', 0, -1, NULL, NULL, NULL, NULL, NULL),
(94, 'https://52dy.hanju2017.com/20190101/16238_6fc0abff/index.m3u8', '敦刻尔克', 'Dunkirk', 0, -1, NULL, NULL, NULL, NULL, NULL),
(95, 'https://135zyv6.xw0371.com/2019/01/06/ecEl8hF4S0PR7jOO/playlist.m3u8', '印度暴徒 高清', 'ydbt', 0, -1, 'https://cdn.youku-letv.com/20190101/13863_85ecb47e/index.m3u8\r\n\r\nThugs of Hindostan\r\n\r\nhttps://135zyv6.xw0371.com/2019/01/02/9Zma5OoXgNe1uQ7p/playlist.m3u8\r\n\r\nhttps://cdn.youku-letv.com/20190104/14192_ecc91b12/index.m3u8', NULL, NULL, NULL, NULL),
(96, 'https://boba.52kuyun.com/20190101/18403_cd5d7804/index.m3u8', '安徽卫视2018国剧盛典', 'gjsd2018', 0, -1, NULL, NULL, NULL, NULL, NULL),
(97, 'http://videos.fjhps.com/20170930/25aUv7Sv/index.m3u8', '销魂玉', 'xhy', 0, -1, '军阀趣史\r\n\r\nhttp://hao.czybjz.com/20171016/Hj39sW1q/index.m3u8', NULL, NULL, NULL, NULL),
(98, 'https://cdn.youku-letv.com/20190105/14347_a63d05d5/index.m3u8', '大人物 抢先版', 'drw', 0, -1, NULL, NULL, NULL, NULL, NULL),
(99, 'https://135zyv6.xw0371.com/2019/01/09/8h34JPNAb3Tyuv1O/playlist.m3u8', '时间怪客 高清', 'sjgk', 0, -1, NULL, NULL, NULL, NULL, NULL),
(100, 'https://52dy.hanju2017.com/20190205/17966_9e4de479/index.m3u8', '手机狂响 高清', 'sjkx', 0, -1, 'https://cdn.youku-letv.com/20190108/14638_89652003/index.m3u8', NULL, NULL, NULL, NULL),
(101, 'https://135zyv6.xw0371.com/2019/01/19/vw1P8BS5wgz0PbEC/playlist.m3u8', '大黄蜂 抢先版', 'dhf', 0, -1, 'https://135zyv6.xw0371.com/2019/01/10/h3KV9l57DiE0LHAg/playlist.m3u8', NULL, NULL, NULL, NULL),
(102, 'http://zy.kubozy-youku-163-aiqi.com/20190303/840_91850b04/index.m3u8', '白蛇：缘起【高清】', 'WhiteSnake', 0, -1, 'https://bobo.kukucdn.com/20190113/3932_b635e80f/index.m3u8', NULL, NULL, NULL, NULL),
(103, 'https://cdn.youku-letv.com/20190115/15064_a900463b/index.m3u8', '超人王朝 超人军团崛起', 'tt7167686', 0, -1, 'Reign of the Supermen', NULL, NULL, NULL, NULL),
(104, 'http://cn2.zuidadianying.com/20190309/1VO52JjF/index.m3u8', '驯龙高手3：隐秘的世界【高清版】', 'xlgs3', 0, -1, 'https://cdn.youku-letv.com/20190114/15063_9509569c/index.m3u8', NULL, NULL, NULL, NULL),
(105, 'https://135zyv6.xw0371.com/2019/01/18/8N4TMoM8FdRasLC0/playlist.m3u8', '密室逃生 抢先版', 'msts', 0, -1, NULL, NULL, NULL, NULL, NULL),
(106, 'https://api.291pai.com:666/2019/02/17/TLfUi7nGsw6VLEA1/playlist.m3u8', '掠食城市 高清', 'MortalEngines', 0, -1, 'https://135zyv6.xw0371.com/2019/01/23/n8pxER5BX7NWdlqN/playlist.m3u8', NULL, NULL, NULL, NULL),
(107, 'https://135zyv6.xw0371.com/2019/01/25/OaHACsx0lJGQcLdX/playlist.m3u8', '罗宾汉：起源 高清', 'RobinHood', 0, -1, NULL, NULL, NULL, NULL, NULL),
(108, 'https://boba.52kuyun.com/20190126/19307_0e18bb8c/index.m3u8', '神奇动物：格林德沃之罪', 'sqdw2', 0, -1, 'https://135zyv6.xw0371.com/2019/01/26/sEteG8nF5MtqA3fr/playlist.m3u8', NULL, NULL, NULL, NULL),
(109, 'http://acfun.iqiyi-kuyun.com/20190129/fYhcvEl5/index.m3u8', 'T-34坦克', 'T34', 0, -1, NULL, NULL, NULL, NULL, NULL),
(110, 'https://135zyv6.xw0371.com/2019/02/12/TCsrw9fCO8i6YIVS/playlist.m3u8', '胡桃夹子和四个王国 国语', 'htjzhsgwg', 0, -1, 'https://135zyv6.xw0371.com/2019/01/30/ngUjnxa8g5gO7gYK/playlist.m3u8', NULL, NULL, NULL, NULL),
(111, 'https://bobo.kukucdn.com/20190129/4991_b7d8a11e/index.m3u8', '2019湖南卫视春节联欢晚会', 'hntv129', 0, -1, NULL, NULL, NULL, NULL, NULL),
(112, 'https://135zyv6.xw0371.com/2019/02/01/yWX127utT4rHK9xH/playlist.m3u8', '家和万事惊', 'jhwsj', 0, -1, 'http://yun.kubozy-youku-163.com/20190201/4176_42320e6f/index.m3u8', NULL, NULL, NULL, NULL),
(113, 'https://135zyv6.xw0371.com/2019/01/31/UMXAOPk7bKzdyECg/playlist.m3u8', '追随者', 'Henchmen', 0, -1, NULL, NULL, NULL, NULL, NULL),
(114, 'http://kakazy-yun.com/20190123/16041_cdc1b9b2/index.m3u8', '绿毛怪格林奇', 'TheGrinch', 0, -1, NULL, NULL, NULL, NULL, NULL),
(115, 'https://52dy.hanju2017.com/20190202/17930_718381b9/index.m3u8', '中国合伙人2', 'zghhr2', 0, -1, NULL, NULL, NULL, NULL, NULL),
(116, 'https://52dy.hanju2017.com/20190203/17944_c3143da8/index.m3u8', '滴答屋 国语', 'ddw', 0, -1, NULL, NULL, NULL, NULL, NULL),
(117, 'https://cdn.youku-letv.com/20190203/16367_00e1c22a/index.m3u8', '霸王行动', 'Overlord', 0, -1, NULL, NULL, NULL, NULL, NULL),
(118, 'https://v.yongjiujiexi.com/20180315/qPhry7pq/index.m3u8', '西游记女儿国', 'xyjneg', 0, -1, NULL, NULL, NULL, NULL, NULL),
(119, 'http://iqiyi.kuyun-bofang.com/20190204/qPfdV7kP/index.m3u8', '2018郑秀文演唱会', 'sammi2018', 0, -1, NULL, NULL, NULL, NULL, NULL),
(120, 'https://youku.com-movie-youku.com/20190311/11928_ba640a44/index.m3u8', '神探蒲松龄【高清版】', 'stpsl', 0, -1, 'https://135zyv6.xw0371.com/2019/02/05/vgthSXBYlqiL0xoA/playlist.m3u8\r\n\r\nhttps://zk.wb699.com/2019/02/08/X7oKtzPtJDNShzyz/playlist.m3u8\r\n\r\nhttp://yun.kubozy-youku-163.com/20190310/7156_cb67d1ab/index.m3u8', NULL, NULL, NULL, NULL),
(121, 'https://135zyv6.xw0371.com/2019/03/07/32KMhU2P8TtFsHik/playlist.m3u8', '疯狂的外星人【高清版】', 'CrazyAlien', 0, -1, 'https://135zyv6.xw0371.com/2019/02/05/iacVV07JpNv23v8a/playlist.m3u8\r\n\r\nhttps://v7.438vip.com/2019/02/07/VdWaYSivVMT3N1W8/playlist.m3u8', NULL, NULL, NULL, NULL),
(122, 'https://v7.438vip.com/2019/03/09/Fek6DKODjQxtwIdY/playlist.m3u8', '新喜剧之王【高清版】', 'xxjzw', 0, -1, 'https://135zyv6.xw0371.com/2019/02/05/mCVEKOnfa7Ns71FS/playlist.m3u8\r\n\r\nhttps://135zyv6.xw0371.com/2019/02/07/ldPb3EVE4D4xVDhG/playlist.m3u8', NULL, NULL, NULL, NULL),
(123, 'https://v7.438vip.com/2019/02/07/WqhSZ53bD8CWILGf/playlist.m3u8', '飞驰人生 高清', 'Pegasus', 0, -1, 'https://135zyv6.xw0371.com/2019/02/06/h37cuiYnbjkxYzrT/playlist.m3u8\r\n\r\nhttps://cdn.youku-letv.com/20190206/16618_fc419857/index.m3u8', NULL, NULL, NULL, NULL),
(124, 'https://v7.438vip.com/2019/02/07/pWmwXjBn5xFbirAr/playlist.m3u8', '流浪地球 高清', 'lldq', 0, -1, 'https://cdn.youku-letv.com/20190206/16605_eb1a2ccf/index.m3u8\r\n\r\nhttps://cdn.youku-letv.com/20190206/16619_3da77109/index.m3u8', NULL, NULL, NULL, NULL),
(125, 'https://135zyv6.xw0371.com/2019/02/07/Pgo4WTiEAWaGB52J/playlist.m3u8', '熊出没·原始时代', 'xcm6', 0, -1, NULL, NULL, NULL, NULL, NULL),
(126, 'http://sohu.zuida-163sina.com/20190208/eiOCDuUw/index.m3u8', '战斗民族养成记电影版', 'zdmzycj', 0, -1, NULL, NULL, NULL, NULL, NULL),
(127, 'http://yun.kubozy-youku-163.com/20190222/5360_748a6baa/index.m3u8', '云南虫谷【高清】', 'yncg', 0, -1, 'https://v7.438vip.com/2019/02/10/48tczqTQZ7sg74eb/playlist.m3u8', NULL, NULL, NULL, NULL),
(128, 'https://cdn.youku-letv.com/20190205/16592_07445cc6/index.m3u8', '一条狗的回家路 抢先版', 'ytgdhjl', 0, -1, NULL, NULL, NULL, NULL, NULL),
(129, 'http://bili.meijuzuida.com/20190203/1999_48456457/index.m3u8', '宝莱坞机器人之恋2：重生归来 2.0 抢先版', 'Enthiran2', 0, -1, 'https://cdn.youku-letv.com/20190205/16591_440b499e/index.m3u8', NULL, NULL, NULL, NULL),
(130, 'https://v7.438vip.com/2019/02/14/1tLsl4XaUmSvEdEz/playlist.m3u8', '唐伯虎点秋香2019', 'tbhdqx2019', 0, -1, NULL, NULL, NULL, NULL, NULL),
(131, 'https://zk.wb699.com/2019/02/15/63kOjTHGVNyaXTjI/playlist.m3u8', '地球最后的夜晚', 'dqzhdyw', 0, -1, NULL, NULL, NULL, NULL, NULL),
(132, 'https://v-6-cn.com/20190216/10404_1b201ba9/index.m3u8', '一吻定情 抢先版', 'ywdq', 0, -1, NULL, NULL, NULL, NULL, NULL),
(133, 'https://zk.wb699.com/2019/02/23/b4E2feBY5DaZOFmv/playlist.m3u8', '阿丽塔：战斗天使', 'Alita', 0, -1, NULL, NULL, NULL, NULL, NULL),
(134, 'https://cdn.youku-letv.com/20190219/17412_b35218ae/index.m3u8', '绿皮书', 'GreenBook', 0, -1, NULL, NULL, NULL, NULL, NULL),
(135, 'https://youku.com-movie-youku.com/20190309/11753_fc3cdc76/index.m3u8', '惊奇队长【抢先版】', 'jqdz', 0, -1, NULL, NULL, NULL, NULL, NULL),
(136, 'http://play.urlnk.com/work/git-mirror/legend/dist/http/win.web.rh03.sycdn.kuwo.cn/1f58101ecdede36c44c07edc3d70ab62/5ca9e6e3/resource/a3/33/39/2785686742.aac', '漂洋过海来看你', NULL, 0, -1, 'http://221.180.170.26/cache/win.web.rh03.sycdn.kuwo.cn/9260a1a81ed77a6c88610fa7f2063e96/5ca54447/resource/a3/33/39/2785686742.aac', 1554337443, 1554338028, 1554338028, 1),
(137, 'http://play.urlnk.com/work/git-mirror/legend/dist/http/39.137.1.205/cache/win.web.nf03.sycdn.kuwo.cn/3f4c51572c24b730f7f7a2d7e25120f3/5ca579b0/resource/a2/52/31/1052045681.aac', '失忆 - 图小图', NULL, 0, -1, NULL, 1554610134, 1554610134, NULL, NULL),
(138, 'http://play.urlnk.com/work/git-mirror/legend/dist/http/101.44.1.3/files/207200000777028B/fs.pc.kugou.com/201810281300/d170ddece079defa7c7895eb68b54bcb/G099/M06/13/05/ow0DAFvFx9CAJCIdABh3TJiu_Js628.mp3', '地铁等待 - 宋梦君', NULL, 0, -1, NULL, 1554635673, 1554635742, 1554635742, 1),
(139, 'http://play.urlnk.com/work/git-mirror/legend/dist/http/101.44.1.10/files/3109000007C7BD5F/fs.pc.kugou.com/201902271155/1230e9ef02961516a4525342f52d3552/G145/M0A/0A/13/0Q0DAFx0wGiAGvdiAD1mAaE-HeY387.mp3', '何为永恒 - 胡夏', NULL, 0, -1, NULL, 1554636103, 1554636103, NULL, NULL),
(140, 'http://videox3.ju1zhe.com:8091/20190317/BEX20FqY/index.m3u8', NULL, NULL, 0, -1, NULL, 1557215361, 1557285114, 1557285114, 2),
(141, 'https://cdn.800zy99.com/20190425/RQ7CgFgo/index.m3u8', NULL, NULL, 0, -1, NULL, 1557215417, 1557299910, 1557299910, 2),
(142, 'http://videox3.ju1zhe.com:8091/20190505/59TWANJL/index.m3u8', NULL, NULL, 0, -1, NULL, 1557215774, 1557300361, 1557300361, 1),
(143, 'http://videox3.ju1zhe.com:8091/20190505/UIrUtqEC/index.m3u8', NULL, NULL, 0, -1, NULL, 1557215857, 1557303915, 1557303915, 1),
(144, 'http://videox3.ju1zhe.com:8091/20190505/IuJDnkmC/index.m3u8', NULL, NULL, 0, -1, NULL, 1557215904, 1557304290, 1557304290, 1),
(145, 'https://ddppnew1.135-cdn.com/20190511/8M0FDNR8/index.m3u8', '雷霆沙赞！', NULL, 0, -1, NULL, 1557678258, 1557726396, 1557726396, 2);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
