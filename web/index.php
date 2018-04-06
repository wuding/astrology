<?php
// 错误报告
if (isset($_GET['debug'])) {
    ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}
// 调试信息
if (isset($_GET['phpinfo']) || '/phpinfo' == @$_SERVER['PATH_INFO']) {
	phpinfo();
	exit;
}


/* 启动 Astrology */
require __DIR__ . '/../app/bootstrap.php';
new Text_Autoload_Loader();
new Astrology\Start();


// 调试问题
if (!empty($_GET['debug'])) {
	eval($_GET['debug']);
}
