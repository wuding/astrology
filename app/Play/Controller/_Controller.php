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
        $this->_view_script = "Index/play";
    }

    public function __call($name, $arguments)
    {
        // 定义
        $m3u8 = new \DbTable\HlsM3u8;
        $hide = 0;
        $where = null;

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
        }
        $arr = $m3u8->fetchAll($where, 'title,name');
        return get_defined_vars();
    }
}
