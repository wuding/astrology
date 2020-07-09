<?php

namespace Controller;

use Astrology\Route;

class _Controller extends \Astrology\Controller
{
    public $tongji = null;
    public $query_args = [];

    public function __construct()
    {
        parent::__construct();

        # header("Access-Control-Allow-Origin: *");

        /* 查询数组 */
        // 默认修正值定义
        $arr = [
            'page' => (object) [1, FILTER_VALIDATE_INT],
            'limit' => (object) [1, FILTER_VALIDATE_INT],
            'type' => '',
        ];
        $this->query_args = $this->array_variable($arr, 1);
        $downloadDir = $GLOBALS['_CONFIG']['downloadDir'];
        $vars = [
            'downloadDir' => $downloadDir,
        ];
        $this->robotVars = array_merge($this->query_args, $vars);
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
                    $filename = 'tmp/tb/alimama_cookie.txt';
                    file_put_contents($filename, $cookie);
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
        $cookie_mm = isset($_SESSION['cookie']) ? $_SESSION['cookie'] : '';
        if ('cookie' == $do) {

            if ($_POST) {
                $cookie = trim($_POST['cookie']);
                if ($cookie) {
                    $_SESSION['taobao_cookie'] = $cookie;
                    $filename = 'tmp/tb/taobao_cookie.txt';
                    file_put_contents($filename, $cookie);
                } else {
                    unset($_SESSION['taobao_cookie']);
                }

            }
            $this->_view_script = "Index/cookie";
            return ['cookie' => $cookie];
        }

        $this->_view_script = "Index/taobao";
        $arr = array('task', 'millisec');
        return $result = $this->array_variable($arr) + ['cookie' => $cookie, 'cookie_mm' => $cookie_mm];
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

        $robot = new $class($this->robotVars);
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
        if (!$msg && $this->query_args['page'] < $result['pageCount']) {
            $this->query_args['page']++;
            $encoded_string = http_build_query($this->query_args);
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
        if ('json' == $this->query_args['type']) {
            return json_encode($value);
        }
        return $value;
    }
}
