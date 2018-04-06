<?php

namespace Astrology;

class Controller
{
	public static $_methods = [];
	public static $_method_name = false;
	
	public function __construct()
	{
	}
	
	public function _NotFound()
	{
		print_r([__METHOD__, __FILE__, __LINE__]);
	}
	
	public function _getMethods()
	{
		return self::$_methods = get_class_methods($this);
	}
	
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
	
	public function _run()
	{
		$method = $this->_getMethodName();
		return $this->$method();
	}
	
	public function __destruct()
	{
		$variables = $this->_run();
		print_r([$variables, __FILE__, __LINE__]);
	}
}
