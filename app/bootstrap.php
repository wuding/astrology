<?php
define('APP_PATH', __DIR__);

// 定义类库和加载规则
define('ANFORA_LIBRARY', APP_PATH . '/../lib');
define('ANFORA_AUTOLOAD', 
	[
		'Astrology|DbTable|Plugin' =>  APP_PATH . '/../src',
		'Controller|View|Model|Form' => APP_PATH . '/{$GLOBALS["MODULE_NAME"]}',
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
