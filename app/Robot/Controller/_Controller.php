<?php

namespace Controller;

use Astrology\Route;

class _Controller extends \Astrology\Controller
{
	public function __construct()
	{
		# header("Access-Control-Allow-Origin: *");
		$this->page = $this->_get('page', 1, FILTER_VALIDATE_INT);
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
		$attr = [
			'page' => $this->page,
		];
		
		$robot = new $class($attr);
		if (!empty($robot->func_format)) {
			$type .= ' ' . $robot->func_format;
		}
		$method = lcfirst($route->fixName($action . ' ' . $type));
		
		$result = $robot->$method();
		$result['pageCount'] = 1084;# 542
		
		$code = 0;
		$msg = '';
		$url_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
		parse_str($_SERVER['QUERY_STRING'], $query_data);		
		if ($this->page < $result['pageCount']) {
			$query_data['page'] = $this->page + 1;
			$encoded_string = http_build_query($query_data);			
			$msg = 'http://lan.urlnk.com' . $url_path .'?'. $encoded_string;

		} else {
			$code = 1;
			$msg = 'final';
		}
		
		$value = [
			'code' => $code,
			'msg' => $msg,
			'data' => $result
		];
		return json_encode($value);
	}
}
