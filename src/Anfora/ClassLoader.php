<?php

namespace Anfora;

class ClassLoader
{
	private static $instance;
	
	public function __construct()
	{
	}
	
	public static function getLoader()
	{
		if (null !== self::$instance) {
			return self::$instance;
		}
		if (!defined('ANFORA_PATH')) {
			define('ANFORA_PATH', __DIR__);
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
			include_file($file);
			return true;
		}
		return false;
	}
	
	public function findFile($class)
	{
		$file = $class;
		$rule = [
			'Anfora' => ANFORA_PATH . '/..',
		];
		if (defined('ANFORA_AUTOLOAD') && is_array(ANFORA_AUTOLOAD)) {
			$rule = array_merge($rule, ANFORA_AUTOLOAD);
		}
		foreach ($rule as $key => $value) {
			$parttern = "/^($key)(\\\.+|)$/i";
			if (preg_match('/^\//', $key)) {
				$parttern = $key;
			}
			if (preg_match($parttern, $class, $matches)) {
				eval("\$value = \"" . $value . "\";");
				$file = $value . '/' . $class;
				break;
			}
		}
		return $file .= '.php';
	}
}

function include_file($file) {
	return include_once $file;
}
