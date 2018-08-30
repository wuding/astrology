<?php

namespace Controller;

use Astrology\Route;

class _Controller extends \Astrology\Controller
{
	public $tongji = null;
	
	public function __construct()
	{
		# header("Access-Control-Allow-Origin: *");
		$this->page = $this->_get('page', 1, FILTER_VALIDATE_INT);
		$this->limit = $this->_get('limit', 1, FILTER_VALIDATE_INT);
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
			'limit' => $this->limit,
		];
		
		$robot = new $class($attr);
		if (!empty($robot->func_format)) {
			$type .= ' ' . $robot->func_format;
		}
		$method = lcfirst($route->fixName($action . ' ' . $type));
		
		$result = $robot->$method();
		if (!isset($result['pageCount'])) {
			$result['pageCount'] = 1; # 542 1084
		}
		
		$code = 0;
		$msg = '';
		// 自动下一页
		if ($this->page < $result['pageCount']) {
			parse_str($_SERVER['QUERY_STRING'], $query_data);
			$query_data['page'] = $this->page + 1;
			$encoded_string = http_build_query($query_data);
			$url_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);			
			$msg = $robot->api_host . $url_path .'?'. $encoded_string;

		} else {
			$code = 1;
			$msg = 'final';
			
			// 继承
			if (isset($result['msg']) && $result['msg']) {
				$msg = $result['msg'];
				unset($result['msg']);
			}
			if (isset($result['code']) && is_numeric($result['code'])) {
				$code = $result['code'];
				unset($result['code']);
			}
		}
		
		$value = [
			'code' => $code,
			'msg' => $msg,
			'data' => $result
		];
		if (isset($_GET['type']) && 'json' == $_GET['type']) {
			$value =json_encode($value);
		}
		return $value;
	}
}
