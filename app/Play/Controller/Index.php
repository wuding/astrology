<?php
/**
 * 播放器默认首页
 */
namespace Controller;

use Astrology\Route;
use DbTable\HlsM3u8;

class Index extends _Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function __call($name, $arguments)
    {
        return [$name, $arguments, __FILE__, __LINE__];
    }

    public function index()
    {
        // 定义
        $m3u8 = new HlsM3u8;
        $hide = 0;

        // 查询
        $playlist = isset($_GET['playlist']) ? trim($_GET['playlist']) : '';
        $query = isset($_GET['q']) ? trim($_GET['q']) : '';
        $query_title = isset($_GET['title']) ? $_GET['title'] : null;
        $cudr = _isset($_GET, 'edit', []);

        // 视图
        $mirror_host = $GLOBALS['CONFIG']['view']['mirror_host'];
        $cdn_host = $GLOBALS['CONFIG']['view']['cdn_host'];
        $tongji = $this->tongji;
        $title = '在线M3U8播放器';

        $like = $url = '';
        $arr = array();

        // 查询类型
        if ($query) {
            if (preg_match('/^http(|s):\/\//', $query)) {
                $url = $query;
                $data_arr = array();
                if ($query_title) {
                    $data_arr['title'] = $query_title;
                }
                $data_arr = array_merge($data_arr, $cudr);
                $m3u8->where_array = array('url' => $query);
                $arr = $m3u8->exist($data_arr);

            } else {
                $like = $query;
            }
        }

        // 查找数据
        $where = "status > 1";
        if ($like) {
            $like = addslashes($like);
            $where = "status > 0 AND title LIKE '%$like%'";
        } elseif ($playlist) {
            $where = "status > 0 AND playlist='$playlist'";
        }
        $arr = $m3u8->fetchAll($where, 'm3u8_id,title,name,url');

        // 非URL
        if (!$url) {
            // 随机ID
            $only = 0;
            $id = 0;
            $rows = null;
            if ($arr) {
                $max = $arr[0]->m3u8_id;
                $only = count($arr);
                // 从中取一个
                if ($like) {
                    /*
                    $ids = [];
                    $data = [];
                    foreach ($arr as $key => $value) {
                        $ids[] = $value->m3u8_id;
                        $data[$value->m3u8_id] = $value;
                    }
                    $k = mt_rand(0, $only);
                    $id = $ids[$k];
                    $rows = $data[$id];
                    */
                    $rows = $arr[0];
                }
            } else {
                $max = $m3u8->count();
            }

            if (1 === $only) {
                # $id = $max;
                $rows = $arr[0];
            } else {
                $id = mt_rand(1, $max);
            }
            // 单条
            $row = $rows ? : $m3u8->get(['m3u8_id' => $id], 'title,url');
            if ($row) {
                $url = $row->url;
                $title = $row->title;
                $like = $like ? : $title;
            }
        }

        $vars = array(
            'mirror_host' => $mirror_host,
            'cdn_host' => $cdn_host,
            'tongji' => $tongji,
            'title' => $title,
            'like' => $like,
            'url' => $url,
            'arr' => $arr,
        );
        return $vars;
    }
}
