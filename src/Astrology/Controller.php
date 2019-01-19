<?php

namespace Astrology;

class Controller
{
    public static $_methods = [];
    public static $_method_name = false;
    public $_view_script = null;
    public $_enable_session = 1;
    public $_enable_view = 1;
    public $_session_id = null;
    public $_init = null;
    
    public function __construct($init = null)
    {
        // 开启会话
        if ($this->_enable_session && !$init) {
            if ($this->_session_id) {
                session_id($this->_session_id);
            }
            $cookie_domain = $this->_getConfig('session', 'cookie_domain', $_SERVER['HTTP_HOST']);
            $cookie_lifetime = $this->_getConfig('session', 'cookie_lifetime', 0);
            session_set_cookie_params($cookie_lifetime, '/', $cookie_domain, false, true);
            session_start();
        }
        $this->_init = $init;
    }
    
    public function _getConfig($section = null, $key = null, $default = null)
    {
        $arr = isset($GLOBALS['CONFIG']) ? $GLOBALS['CONFIG'] : [];
        $var = isset($arr[$section]) ? $arr[$section] : [];
        $val = isset($var[$key]) ? $var[$key] : $default;
        return $val;
    }

    public static function varArr($arr = array(), $query_merge = null)
    {
        $instance = new static(1);
        return $instance->array_variable($arr, $query_merge);
    }
    
    public function array_variable($arr = array(), $query_merge = null)
    {
        $result = array();
        parse_str($_SERVER['QUERY_STRING'], $query_result);

        foreach ($arr as $key => $value) {
            $idx = $key;
            if (is_numeric($key)) {
                $idx = $key = $value;               
                $value = null;
            }

            if (is_object($value)) {
                $arr = (array) $value;
                $value = $arr[0];
                $filter = $arr[1];
                $_variables = isset($arr[2]) ? $arr[2] : null;
                $result[$idx] = $this->_var($key, $value, $filter, $_variables);
            } else {
                $result[$idx] = $this->_var($key, $value);
            }
            
        }

        // 合并数组
        switch ($query_merge) {
            case 1:
                $result = array_merge($query_result, $result);
                break;
        }
        
        return $result;
    }
    
    /*
     * 输入 - 变量
     */

    public function _var($key = null, $default = false, $filter = null, $_variables = null)
    {
        if (null === $_variables) {
            $_variables = $_GET;
        }
        
        $var = isset($_variables[$key]) ? $_variables[$key] : null;
        if (null !== $var) {
            if (is_array($var)) {
                return $var;
            }
            if (is_array($filter)) {
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
        if ($this->_init) {
            return $this->_init;
        }

        $arr = $this->_run();
        // 调试
        if (isset($_GET['debug']) && is_numeric($_GET['debug'])) {
            print_r([$arr, __FILE__, __LINE__]);
            exit; # 
        }
        
        // 视图渲染
        if ($this->_enable_view) {
            if ('info' === $this->_enable_view) {
                print_r([$arr, __METHOD__, __LINE__, __FILE__]);
            } else {
                $var = is_array($arr) ? $arr : [];
                $m = $GLOBALS['MODULE_NAME'];
                $c = $GLOBALS['CONTROLLER_NAME'];
                $a = $GLOBALS['ACTION_NAME'];
                $script = $this->_view_script ? : "$c/$a";
                $tpl = new \League\Plates\Engine(APP_PATH . "/$m/View");
                echo $html = $tpl->render($script, $var);# 
            }
            
        // 直接输出
        } else {
            $variables = is_array($arr) ? print_r($arr, true) : $arr;# 
            echo $variables;# 
        }
        # print_r($GLOBALS);
    }
}
