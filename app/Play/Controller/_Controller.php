<?php
/**
 * 按名称播放条目
 */
namespace Controller;

use Astrology\Route;

class _Controller extends \Astrology\Controller
{
    public $tongji = null;
    public $playlist = [];

    public function __construct()
    {
        session_start();
        $view_script = "Index/play";

        // 退出
        if (isset($_GET['logout'])) {
            $this->_logout();
        }

        // 登录
        if (!$this->_session() && !isset($_GET['logout'])) {
            $this->_auth();
            $view_script = 'Index/auth';
        }

        // 统计
        $stat = 0;
        if (isset($_GET['stat'])) {
            $stat = $_GET['stat'];
            setcookie('stat', $stat, time()+60*60*24*30, '/');
        } elseif (isset($_COOKIE['stat'])) {
            $stat = $_COOKIE['stat'];
        }
        $this->tongji = $stat;

        $GLOBALS['MODULE_NAME'] = '_Module';
        $this->_view_script = $view_script;
    }

    public function __call($name, $arguments)
    {
        $PATHS = $GLOBALS['PATHS'];
        array_shift($PATHS);
        array_shift($PATHS);
        $play_name = implode('/', $PATHS);
        if ($play_name != $name) {
            $name = $play_name;
        }
        # print_r([$name, $play_name, $PATHS]);exit;

        // 定义
        $m3u8 = new \DbTable\HlsM3u8;
        $hide = 0;
        $where = array('status > 1');

        // 视图
        $cdn_host = $GLOBALS['CONFIG']['view']['cdn_host'];
        $tongji = $this->tongji;
        $title = '在线M3U8播放器';

        $like = $url = '';
        $row = $m3u8->findByName($name);
        if ($row) {
            $url = $row->url;
            $like = $title = $row->title;
            $hide = 1;
            if ($row->playlist) {
                $where = ['playlist' => $row->playlist];
            }
        } else {
            header('Location: /play?q=' . urlencode($name));
        }
        $arr = $m3u8->fetchAll($where, 'title,name');
        return get_defined_vars();
    }

    public function _session()
    {
        $authorized = $_SESSION['auth'] = null;
        $user = $pass = $GLOBALS['CONFIG']['auth'];

        if (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW']) && isset($_SESSION['authorized'])) {
            # print_r($_SERVER);
            if ($user == $_SERVER['PHP_AUTH_USER'] && $pass == $_SERVER['PHP_AUTH_PW']) {
                $authorized = $_SESSION['auth'] = true;
            }
        }
        return $authorized;
    }

    public function _auth()
    {
        header('WWW-Authenticate: Basic Realm="Login please"');
        header('HTTP/1.0 401 Unauthorized');
        $_SESSION['authorized'] = true;
    }

    public function session_get($key, $value = null)
    {
        $get = $value;
        if (isset($_SESSION[$key])) {
            if (!$value) {
                $get = $_SESSION[$key];
            } elseif (!$_SESSION[$key]) {
            } else {
                $get = $_SESSION[$key];
            }
        }
        return $get;
    }

    public function _logout()
    {
        $_SESSION = array();
        unset($_COOKIE[session_name()]);
        session_destroy();
        echo 'logging out ...';
    }
}
