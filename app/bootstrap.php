<?php
define('APP_PATH', __DIR__);

/**
 * 定义模块和控制器名称
 *
 * array() 检测这个数组
 * true 检测模块目录和控制器文件是否存在
 * false 不检测
 */
define('APP_MODULES', true/*
	[
		'_Module' => ['_Controller', 'Robot'],
		'Robot' => ['_Controller'],
	]*/
);

/**
 * 定义类加载规则
 *
 * | 分隔多个前缀
 * // 自定义正则匹配模式
 * .* 匹配所有其它，如果不设置这条将从 include_path 查找
 * 顺序很重要
 */
define('ANFORA_AUTOLOAD', 
	[
		'Astrology|DbTable|Plugin' =>  APP_PATH . '/../src',
		'Controller|View|Model|Form' => APP_PATH . '/{$GLOBALS["MODULE_NAME"]}',
		'/_/' => ['eval' => '$arr = explode("_", $name); $name = array_pop($arr); $path = "' . APP_PATH . '/../lib/" . implode("/", $arr);'],# 
		# '.*' => APP_PATH . '/../lib',
	]
);


/* 引入类加载器 */
if (ANFORA_AUTOLOAD) {
    require APP_PATH . '/../src/Anfora/autoload.php';
} else {
    require APP_PATH . '/../vendor/autoload.php';
}

# include_once 'function.php';
