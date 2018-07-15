<?php

namespace Astrology;

class Controller
{
	public static $_methods = [];
	public static $_method_name = false;
	
	public function __construct()
	{
	}
	
	/**
	 * 获取变量参数
	 */
	public function _get($key = null, $default = false, $filter = FILTER_UNSAFE_RAW)
	{
		$var = isset($_GET[$key]) ? $_GET[$key] : null;
		if (null !== $var) {
			if (is_array($var)) {
                return $var;
            } elseif (is_array($filter)) {
                if (in_array($var, $filter)) {
                    $var = false;
                } 
            } elseif (null !== $filter) {
                $var = filter_var($var, $filter);
            }
			
			if (false !== $var) {
                return $var;
            }
		}
		return $default;
	}
	
	/**
	 * 未找到动作
	 */
	public function _NotFound()
	{
		print_r([__METHOD__, __FILE__, __LINE__]);
	}
	
	/**
	 * 获取类的所有方法
	 */
	public function _getMethods()
	{
		return self::$_methods = get_class_methods($this);
	}
	
	/**
	 * 获取动作方法
	 */
	public function _getMethodName()
	{
		if (false !== self::$_method_name) {
			return self::$_method_name;
		}
		
		$methods = $this->_getMethods();
		if (!in_array($GLOBALS['METHOD_NAME'], $methods) && false === array_search('__call', $methods)) {
			$GLOBALS['METHOD_NAME'] = in_array('_Action', $methods) ? '_Action' : '_NotFound';
		}
		return self::$_method_name = $GLOBALS['METHOD_NAME'];
	}
	
	/**
	 * 执行动作
	 */
	public function _run()
	{
		$method = $this->_getMethodName();
		return $this->$method();
	}
	
	/**
	 * 析构函数
	 */
	public function __destruct()
	{
		$variables = $this->_run();
		if (isset($_GET['debug'])) {
			$variables = is_array($variables) ? print_r($variables, true) : $variables;
			print_r([$variables, __FILE__, __LINE__]);
			# exit;
		}
	}
}
