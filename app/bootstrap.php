<?php
define('APP_PATH', __DIR__);
define('ANFORA_AUTOLOAD', 
	[
		'Astrology' =>  APP_PATH . '/../src',
		'Controller|View|Model' => APP_PATH . '/{$GLOBALS["MODULE_NAME"]}'
	]
);

if (ANFORA_AUTOLOAD) {
    require APP_PATH . '/../src/Anfora/autoload.php';
} else {
    require APP_PATH . '/../vendor/autoload.php';
}

# include_once 'function.php';
