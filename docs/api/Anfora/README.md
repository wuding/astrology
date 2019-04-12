# Anfora 

> index.php

```php
<?php
require_once __DIR__ . '/../app/bootstrap.php';
new Astrology\Start();
```



> bootstrap.php

```php
<?php
$ANFORA_AUTOLOAD = [
    'Controller|View|Model|Form' => APP_PATH . '/{$GLOBALS["MODULE_NAME"]}',
    '/_/' => ['eval' => '$arr = explode("_", $name); $name = array_pop($arr); $path = "' . APP_PATH . '/../lib/" . implode("/", $arr);'],
];

if (ANFORA_AUTOLOAD) {
    $ClassLoader = require APP_PATH . '/../src/Anfora/autoload.php';
} else {
    $ClassLoader = require APP_PATH . '/../vendor/autoload.php';
}
```



> autoload.php
>

```php
<?php
require_once 'ClassLoader.php';
# return Anfora\ClassLoader::getLoader();
$anfora = Anfora\ClassLoader::getLoader();
```



Anfora\ClassLoader 目录位置，文件队列，获取注册注销加载查找

Anfora\Import