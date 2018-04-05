<?php
define('APP_PATH', __DIR__);

// 定义类加载规则
define('ANFORA_AUTOLOAD', 
	[
		'Astrology' =>  APP_PATH . '/../src',
		'Controller|View|Model' => APP_PATH . '/{$GLOBALS["MODULE_NAME"]}'
	]
);

/* 引入类加载器 */
if (ANFORA_AUTOLOAD) {
    require APP_PATH . '/../src/Anfora/autoload.php';
} else {
    require APP_PATH . '/../vendor/autoload.php';
}

# include_once 'function.php';
