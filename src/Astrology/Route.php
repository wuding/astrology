<?php

namespace Astrology;

class Route
{
	private static $instance;
	public $request_path = false;
	public $request_query = false;
	public $request_param = [];
	public $params = [];
	public $query = false;
	public $extension = false;
	public $path = false;
	
	public function __construct()
	{
		// 路由规则
		$route = $GLOBALS['CONFIG']['route'];
		$tmp = $this->getRequestPath(0);
		foreach ($route as $key => $value) {
			# print_r(array($key, $value, $tmp));
			if (preg_match("/$key/i", $tmp, $matches)) {
				$this->request_path = $value;
				break;
			}
		}
		
		$this->path = $this->getRequestPath(2);
	}
	
	public static function getInstance()
	{
		if (null != self::$instance) {
			return self::$instance;
		}
		return self::$instance = new Route();
	}
	
	public function getRequestPath($set = 1)
	{
		if (1 == $set && false !== $this->request_path) {
			return $this->request_path;
		}
		$request_uri = preg_replace('/\/+/', '/', $_SERVER['REQUEST_URI']);
		$path = parse_url($request_uri, PHP_URL_PATH);		
		$path = str_replace($this->getScriptPath(), '', urldecode($path));
		$path = trim($path);
		if (preg_match('/(.*)\.(php|html|htm)$/i', $path, $matches)) {
			$path = $matches[1];
			$this->extension = $matches[2];
		}
		if ($set) {			
			if (2 == $set) {
				return $path;
			}
			$this->request_path = $path;
		}
		return $path;
	}
	
	public function getRequestQuery()
	{
		if (false !== $this->request_query) {
			return $this->request_query;
		}
		return $this->request_query = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);
	}
	
	public function getRequestParam($default = null, $index = 4)
	{
		if (isset($this->request_param[$index])) {
			return $this->request_param[$index];
		}
		$path = explode('/', $this->getRequestPath(), $index + 1);
		if (isset($path[$index]) && ($name = trim($path[$index]))) {
			$default = $name;
		}
		return $this->request_param[$index] = trim($default, '/');
	}
	
	public function getPath($key = 0, $default = null)
	{
		$path = explode('/', $this->getRequestPath());
		# print_r($path);
		if (isset($path[$key]) && ($name = $path[$key])) {
			return $name;
		}
		return $default;
	}
	
	public function getModuleName($default = null, $index = 1)
	{
		$path = $this->getPath($index, $default);
		$fixname = $this->fixName($path);
		# print_r([$this->getRequestPath(), $path, $fixname]);
		return $fixname;
	}
	
	public function getControllerName($default = null, $index = 2)
	{
		return $this->fixName($this->getPath($index, $default));
	}
	
	public function getActionName($default = null, $index = 3)
	{
		return lcfirst($this->fixName($this->getPath($index, $default)));
	}
	
	public function getParams($pattern = 0, $default = null, $index = 4)
	{
		$request = $index . $pattern;
		if (isset($this->params[$request])) {
			return $this->params[$request];
		}
		$request_param = $this->getRequestParam($default, $index);
		$param = $arr = [];
		$count = 0;
		if ($request_param) {
			$param = explode('/', $request_param);
			$count = count($param);
			for ($i = 0; $i < $count; $i++) {
				$value = $param[$i];
				if (1 & $i) {
					$key = $param[$i - 1];
					$arr[$key] = $value;
				} else {
					$arr[$value] = false;
				}
			}
		}
		$all = array_merge($param, $arr);
		switch ($pattern) {
			case 11:
				$all = $count;
				break;
			case 12:
				$all = count($arr);
				break;
			case 10:
				$all = count($all);
				break;
			case 1:
				$all = $param;
				break;
			case 2:
				$all = $arr;
		}
		return $this->params[$request] = $all;
	}
	
	public function getParam($name = null, $default = null, $data = null)
	{
		if (!$data) {
			$data = $GLOBALS['PARAMS'] ? : $this->getParams();
		}
		if (isset($data[$name])) {
			return $data[$name];
		}
		return $default;
	}
	
	public function getQueryArray()
	{
		if (false !== $this->query) {
			return $this->query;
		}
		parse_str($this->getRequestQuery(), $this->query);
		return $this->query;
	}
	
	public function getScriptPath()
	{
		return $this->script_path = str_replace($_SERVER['DOCUMENT_ROOT'], '', $_SERVER['SCRIPT_FILENAME']);
	}
	
	public function fixName($name, $default = false, $separator = '')
	{
		$space = preg_replace("/[^a-z0-9]/i", ' ', $name);
		$word = ucwords($space);
		$both = preg_replace("/^\s|\s$/", '_', $word);
		$gap = preg_replace("/([^\s])\s/", "$1" . $separator, $both);
		$value = preg_replace("/\s/", '_', $gap) ? : $default;
		if (preg_match("/^(\d+)/", $value, $matches)) {
			$value = "_$value";
		}
		return $value;
	}
	
	public function getModules()
	{
		return scandir(APP_PATH);
	}
	
	public function getControllers()
	{
		$dir = scandir(APP_PATH . '/' . $GLOBALS['MODULE_NAME'] . '/Controller');
		$arr = [];
		foreach ($dir as $file) {
			$arr[] = str_replace('.php', '', $file);
		}
		return $arr;
	}
	
	/*
	 * 数组
	 */
    public function array_merge_key($arr = array(), $arr2 = array())
    {
    	$a = count($arr);
    	$b = count($arr2);

    	$ar = array();
    	$i = 0;
    	foreach ($arr as $key => $value) {
    		if (isset($arr2[$key]) && $arr2[$key]) {
    			$value = $arr2[$key];
    		}

    		$ar[$key] = $value;
    		$i++;
    	}

    	if ($b > $a) {
    		for ($j = $i; $j < $b; $j++) {
    			$ar[$j] = $value;
    		}
    	}

    	return $ar;
    }
}
