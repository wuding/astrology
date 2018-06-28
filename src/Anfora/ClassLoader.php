<?php

namespace Anfora;

class ClassLoader
{
	private static $instance;
	
	public function __construct()
	{
		if (!defined('ANFORA_PATH')) {
			define('ANFORA_PATH', __DIR__);
		}
		if (!isset($GLOBALS['ANFORA_IMPORT'])) {
			$GLOBALS['ANFORA_IMPORT'] = [];
		}
	}
	
	public static function getLoader()
	{
		if (null !== self::$instance) {
			return self::$instance;
		}
        self::$instance = new \Anfora\ClassLoader();
        self::$instance->register(true);
        return self::$instance;
	}
	
	public function register($prepend = false)
    {
        spl_autoload_register(array($this, 'loadClass'), true, $prepend);
    }
    
    public function unregister()
    {
        spl_autoload_unregister(array($this, 'loadClass'));
    }
	
	public function loadClass($class)
	{
		if ($file = $this->findFile($class)) {
			Import($file);
			return true;
		}
		return false;
	}
	
	public function findFile($name)
	{
		global $ANFORA_AUTOLOAD;
		$file = $name;
		// 兼容 PEAR 类命名和加载规则
		if (preg_match('/[a-z0-9]_/i', $name)) {
			$arr = explode('_', $name);
			$filename = array_pop($arr);
			$file = implode('/', $arr) . '/' . $filename;
		}
		
		/* 定义规则 */
		$rule = [
			'Anfora' => ANFORA_PATH . '/..',
		];
		if (defined('ANFORA_AUTOLOAD') && is_array($ANFORA_AUTOLOAD)) {
			$rule = array_merge($rule, $ANFORA_AUTOLOAD);
		}
		
		/* 匹配规则 */
		foreach ($rule as $key => $path) {
			$parttern = "/^($key)(\\\.+|)$/i";
			// 自定义正则
			if (preg_match('/^\//', $key)) {
				$parttern = $key;
			}
			if (preg_match($parttern, $name, $matches)) {
				if (is_array($path)) {
					// 增强的自定义规则
					if (isset($path['eval'])) {
						eval($path['eval']);
					}
				} else {
					// 兼容 PEAR 类命名和加载规则
					if (preg_match('/[a-z0-9]_/i', $name)) {
						$arr = explode('_', $name);
						$name = array_pop($arr);
						$path = $path . '/' . implode('/', $arr);
					}
					eval("\$path = \"" . $path . "\";");
				}
				$file = $path . '/' . $name;
				break;
			}
		}
		return $file .= '.php';
	}
}

function Import($file) {
	$GLOBALS['ANFORA_IMPORT'][] = $file;
	return @include $file;
}
