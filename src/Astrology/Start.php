<?php

namespace Astrology;

class Start
{
	public function __construct()
	{
		$GLOBALS['CONFIG'] = \Anfora\Import(APP_PATH . '/config.php');
		# print_r($config);
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
		
		/* 检测模块目录 */
		$module = $route->getModuleName('index');
		if (APP_MODULES) {
			$GLOBALS['MODULES'] = is_array($APP_MODULES) ? array_keys($APP_MODULES) : $route->getModules();
			if (!in_array($module, $GLOBALS['MODULES'])) {
				$module = '_Module';
				$shift--;
			}
		}
		$GLOBALS['MODULE_NAME'] = $module;
		
		/* 检测控制器文件 */
		$controller = $route->getControllerName('index', $shift);
		if (APP_MODULES) {
			$GLOBALS['CONTROLLERS'] = is_array($APP_MODULES) && isset($APP_MODULES[$module]) ? $APP_MODULES[$module] : $route->getControllers();
			if (!in_array($controller, $GLOBALS['CONTROLLERS'])) {
				$controller = '_Controller';
				$shift--;
			}
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
		}
		
		/* 获取动作方法和请求参数 */
		$route = Route::getInstance();
		$shift = $GLOBALS['SHIFT'];
		$GLOBALS['ACTION_NAME'] = $GLOBALS['METHOD_NAME'] = $route->getActionName('index', $shift + 1);
		$GLOBALS['PARAMS'] = $route->getParams(null, null, $shift + 2);
		$GLOBALS['PATH'] = $route->path;
		
		/* 创建控制器类的实例 */
		if (class_exists($class_name)) {
			$controller = new $class_name();
		} else {
			print_r($GLOBALS);
		}
	}
}
