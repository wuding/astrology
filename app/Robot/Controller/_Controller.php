<?php

namespace Controller;

use Astrology\Route;

class _Controller extends \Astrology\Controller
{
	public function __construct()
	{
		# parent::__construct();
	}
	/*
	public function _NotFound()
	{
		print_r([__METHOD__, __FILE__, __LINE__]);
	}
	
	public function _Action()
	{
		print_r([__METHOD__, __FILE__, __LINE__]);
	}
	*/
	public function __call($name, $arguments)
	{
		$route = Route::getInstance();
		$action = $route->getParam(0);
		$type = $route->getParam(1);		
		$class = '\Plugin\Robot\\' . $route->fixName($name);		
		$robot = new $class();
		if (!empty($robot->func_format)) {
			$type .= ' ' . $robot->func_format;
		}
		$method = lcfirst($route->fixName($action . ' ' . $type));
		return $robot->$method();
	}
}
