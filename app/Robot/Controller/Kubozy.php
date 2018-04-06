<?php

namespace Controller;

use Astrology\Route;

class Kubozy extends _Controller
{
	public function __construct()
	{
		# parent::__construct();
		print_r([__METHOD__, __FILE__, __LINE__]);
	}
	
	public function __call($name, $arguments)
	{
		$route = Route::getInstance();
		$action = $GLOBALS['ACTION_NAME'];
		$type = $route->getParam(0);		
		$class = '\Plugin\Robot\Kubozy';
		$robot = new $class();
		if (!empty($robot->func_format)) {
			$type .= ' ' . $robot->func_format;
		}
		$method = lcfirst($route->fixName($action . ' ' . $type));
		return $robot->$method();
	}
}
