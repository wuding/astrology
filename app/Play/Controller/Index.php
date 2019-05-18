<?php
/**
 * 播放器默认首页
 */
namespace Controller;

use Astrology\Route;

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
        $query = isset($_GET['q']) ? trim($_GET['q']) : '';
        $query_title = isset($_GET['title']) ? $_GET['title'] : null;
        $cudr = _isset($_GET, 'edit', []);
        $tongji = $this->tongji;
        $m3u8 = new \DbTable\HlsM3u8;
        $title = '在线M3U8播放器';
        $url = '';
        $hide = 0;
        $like = '';
        // 查询类型
        if ($query) {
            if (preg_match('/^http(|s):\/\//', $query)) {
                $url = $query;
                $data_arr = ['url' => $query, 'title' => $query_title];
                $data_arr = array_merge($data_arr, $cudr);
                $arr = $m3u8->exist($data_arr);

            } else {
                $like = $query;
            }
        }

        // 查找数据
        $where = null;
        if ($like) {
            $like = addslashes($like);
            $where = "title LIKE '%$like%'";
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
                if ($where) {
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
            $row = $rows ? : $m3u8->sel(['m3u8_id' => $id], 'title,url');
            if ($row) {
                $url = $row->url;
                $title = $row->title;
                $like = $like ? : $title;
            }
        }

        $cdn_host = $GLOBALS['CONFIG']['view']['cdn_host'];
        include APP_PATH . '/_Module/View/Index/play.php';
        exit;
    }
}
