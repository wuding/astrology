<?php

namespace Astrology;

use Astrology\Extension\PhpMemcache;

class Start
{
	public $_disable_cache = 0;
	public $cache = null;
	
	public function __construct()
	{
		# $GLOBALS['CONFIG'] = \Anfora\Import::file(APP_PATH . '/config.php');
		$GLOBALS['CONFIG'] = include_once(APP_PATH . '/config.php');
		# print_r($GLOBALS['CONFIG']);
		
		if (!$this->_disable_cache && extension_loaded('memcache')) {# 
			$this->cache = new PhpMemcache;
			$this->cache->connect();
		}# 
		
		$this->initRoute();
		$this->loadController();
	}
	
	/**
	 * 初始化路由
	 * 获取模块和控制器名称
	 */
	public function initRoute()
	{
		global $APP_MODULES;
		$route = Route::getInstance();
		$shift = 2;
		
		/* 获取缓存 */
		$md5 = md5($route->path);
		$this->controller_key = 'controller_' . APP_ID . '_' . $md5;
		$this->route_key = 'route_' . APP_ID . '_' . $md5;
		# $set = $this->cache->set($route_key, []);
		$ROUTES = $this->cache->get($this->route_key);
		# print_r([__line__, $ROUTES]);exit; 
		
		
		if (!$ROUTES) {
			/* 检测模块目录 */
			$module = $route->getModuleName('index');
			if (APP_MODULES) {
				$func = <<<'NOWDOC'
function () {
	$route = \Astrology\Route::getInstance();
	return $modules = $route->appModules();
}
NOWDOC;

				if (is_array($APP_MODULES) && $APP_MODULES) {
					$func = $APP_MODULES;
				}
				# $APP_MODULES = $route->appModules($APP_MODULES);
				
				
				$module_key = 'modules_' . APP_ID;
				$APP_MODULES = $this->cache->check($module_key, $func);
				# $this->cache->set($module_key, []);
				# $APP_MODULES = $this->cache->get($module_key);
				# print_r([$module, __line__, $APP_MODULES]);exit; 
				$GLOBALS['MODULES'] =  array_keys($APP_MODULES);
				if (!in_array($module, $GLOBALS['MODULES'])) {
					$module = '_Module';
					$shift--;
				}
			}
			
		} else {
			$module = $ROUTES['module'];
		}
		$GLOBALS['MODULE_NAME'] = $module;
		
		
		if (!$ROUTES) {
			/* 检测控制器文件 */
			$controller = $route->getControllerName('index', $shift);
			if (APP_MODULES) {
				$func = <<<'NOWDOC'
function () {
	$route = \Astrology\Route::getInstance();
	return $modules = $route->appControllers();
}
NOWDOC;
			
				if (is_array($APP_MODULES) && isset($APP_MODULES[$module]) && $APP_MODULES[$module]) {
					$func = $APP_MODULES[$module];
				}
				$controller_key = 'controllers_' . APP_ID . '_' . $module;
				$APP_MODULES[$module] = $this->cache->check($controller_key, $func);# 
				# print_r([$controller, __line__, $APP_MODULES]);exit; 
				$GLOBALS['CONTROLLERS'] =  $APP_MODULES[$module];
				# print_r([$controller, $APP_MODULES, $GLOBALS['CONTROLLERS']]);exit;
				if (!in_array($controller, $GLOBALS['CONTROLLERS'])) {
					$controller = '_Controller';
					$shift--;
				}
			}
			
			/* 缓存部分路由信息 */
			$ROUTES = [
				'module' => $module,
				'controller' => $controller,
				'shift' => $shift,
			];
			$set = $this->cache->set($this->route_key, $ROUTES);
			# print_r([__line__, $ROUTES]);exit; 
			
		} else {
			$controller = $ROUTES['controller'];
			$shift = $ROUTES['shift'];
		}
		$GLOBALS['CONTROLLER_NAME'] = $controller;
		$GLOBALS['SHIFT'] = $shift;
	}
	
	/**
	 * 加载控制器
	 * 获取动作方法和请求参数
	 */
	public function loadController()
	{
		$route = Route::getInstance();
	
		/* 获取缓存 */
		$ROUTES = $this->cache->get($this->controller_key);
		# print_r([__line__, $ROUTES]);exit;
		if ($ROUTES) {
			$GLOBALS["MODULE_NAME"] = $ROUTES['module'];
			$GLOBALS['CONTROLLER_NAME'] = $ROUTES['controller'];
			$GLOBALS['SHIFT'] = $ROUTES['shift'];
			$GLOBALS['ACTION_NAME'] = $GLOBALS['METHOD_NAME'] = $ROUTES['action'];
			$GLOBALS['PARAMS'] = $ROUTES['params'];
			$class_name = $ROUTES['class'];
		}
		
		if (!ANFORA_AUTOLOAD) {
			$this->composerAutoload('PSR4', 'Controller\\', APP_PATH . '/' . $GLOBALS["MODULE_NAME"] . '/Controller/');
			$this->composerAutoload('PSR4', 'View\\', APP_PATH . '/' . $GLOBALS["MODULE_NAME"] . '/View/');
		}
		
		
		# print_r([__line__, $ROUTES]);exit;
		if (!$ROUTES) {
			
			$class_name = 'Controller\\' . $GLOBALS['CONTROLLER_NAME'];	
			
			/* 仅在目录文件存在而未定义类的情况下才需要下面两个缺省 */
			// 缺省控制器
			if ('_Controller' != $GLOBALS['CONTROLLER_NAME'] && !class_exists($class_name)) {
				$class_name = 'Controller\_Controller';
				$GLOBALS['CONTROLLER_NAME'] = '_Controller';
				$GLOBALS['SHIFT']--;
			}
			// 缺省模块
			if ('_Module' != $GLOBALS['MODULE_NAME'] && !class_exists($class_name)) {
				$GLOBALS['MODULE_NAME'] = '_Module';
				$GLOBALS['SHIFT']--;
				
				if (!ANFORA_AUTOLOAD) {
					$this->composerAutoload('PSR4', 'Controller\\', APP_PATH . '/' . $GLOBALS["MODULE_NAME"] . '/Controller/');
					$this->composerAutoload('PSR4', 'View\\', APP_PATH . '/' . $GLOBALS["MODULE_NAME"] . '/View/');
				}
			}
			
			/* 获取动作方法和请求参数 */
			
			$shift = $GLOBALS['SHIFT'];
			$GLOBALS['ACTION_NAME'] = $GLOBALS['METHOD_NAME'] = $route->getActionName('index', $shift + 1);
			$GLOBALS['PARAMS'] = $route->getParams(null, null, $shift + 2);
			
			/* 缓存完整路由信息 */
			$ROUTES = [
				'module' => $GLOBALS['MODULE_NAME'],
				'controller' => $GLOBALS['CONTROLLER_NAME'],
				'shift' => $shift,
				'action' => $GLOBALS['ACTION_NAME'],
				'params' => $GLOBALS['PARAMS'],
				'class' => $class_name,
			];
			$set = $this->cache->set($this->controller_key, $ROUTES);
			# print_r([__line__, $ROUTES]);exit;
		}
		$GLOBALS['PATH'] = $route->path;
		
		/* 创建控制器类的实例 */
		if (class_exists($class_name)) {
			$controller = new $class_name();
		} else {
			print_r($GLOBALS);
		}
	}
	
	/**
	 * 自动加载
	 * @access public
	 * @param string $type 类型
	 * @param string $prefix 命名空间前缀
	 * @param string $path 类文件路径
	 * @param string $useIncludePath 使用包含路径
	 * @return mixed 
	 */
	public function composerAutoload($type = 'PSR4', $prefix = '', $path = '', $useIncludePath = null)
	{
		$loader = new \Composer\Autoload\ClassLoader();
		switch ($type) {
			case 'PSR4':
				$loader->addPsr4($prefix, $path);
				break;
			default:
				$loader->add($prefix, $path);
		}
			
		$loader->register();
		if (null !== $useIncludePath) {
			$loader->setUseIncludePath($useIncludePath);
		}
	}
}
