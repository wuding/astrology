<?php

namespace Controller;

use Astrology\Route;

class _Controller extends \Astrology\Controller
{
	public $tongji = null;
	
	public function __construct()
	{
		parent::__construct();
		
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
	public function _alimama($do)
	{
		# print_r($GLOBALS);exit;
		if ('cookie' == $do) {
			$cookie = isset($_SESSION['cookie']) ? $_SESSION['cookie'] : '';
			if ($_POST) {
				$cookie = trim($_POST['cookie']);
				if ($cookie) {
					$_SESSION['cookie'] = $cookie;
				} else {
					unset($_SESSION['cookie']);
				}
				
			}
			# $this->_disable_layout = 0;
			$this->_view_script = "Index/cookie";
			return ['cookie' => $cookie];
		}
		
		$this->_view_script = "Index/alimama";
		$cookie = isset($_SESSION['cookie']) ? $_SESSION['cookie'] : '';
		$arr = array('task', 'millisec');
		return $result = $this->array_variable($arr) + ['cookie' => $cookie];
	}
	
	public function _taobao($do)
	{
		$cookie = isset($_SESSION['taobao_cookie']) ? $_SESSION['taobao_cookie'] : '';
		if ('cookie' == $do) {
			
			if ($_POST) {
				$cookie = trim($_POST['cookie']);
				if ($cookie) {
					$_SESSION['taobao_cookie'] = $cookie;
				} else {
					unset($_SESSION['taobao_cookie']);
				}
				
			}
			$this->_view_script = "Index/cookie";
			return ['cookie' => $cookie];
		}
		
		$this->_view_script = "Index/taobao";
		$arr = array('task', 'millisec');
		return $result = $this->array_variable($arr) + ['cookie' => $cookie];
	}
	
	public function __call($name, $arguments)
	{
		if (in_array($name, ['alimama', 'taobao'])) {
			$do = isset($GLOBALS['PARAMS'][0]) ? $GLOBALS['PARAMS'][0] : '';
			if (in_array($do, ['', 'cookie'])) {
				$func = "_$name";
				return $this->$func($do);
			}
		}
		$this->_enable_view = 0;
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
		$extend = 0;
		
		// 继承
		if (isset($result['msg']) && $result['msg']) {
			$msg = $result['msg'];
			unset($result['msg']);
			$extend++;
		}
		if (isset($result['code']) && is_numeric($result['code'])) {
			$code = $result['code'];
			unset($result['code']);
			$extend++;
		}
			
		// 自动下一页
		if ($this->page < $result['pageCount']) {
			parse_str($_SERVER['QUERY_STRING'], $query_data);
			$query_data['page'] = $this->page + 1;
			$encoded_string = http_build_query($query_data);
			$url_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);			
			$msg = $robot->api_host . $url_path .'?'. $encoded_string;

		} elseif (!$extend) {
			$code = 1;
			$msg = 'final';
		}
		# print_r($result);exit;
		
		$value = [
			'code' => $code,
			'msg' => $msg,
			'data' => $result
		];
		if (isset($_GET['type']) && 'json' == $_GET['type']) {
			$value = json_encode($value);
		}
		return $value;
	}
}
