<?php
/**
 * 网易云音乐
 *
 * @author     Administrator
 * @since      2020
 */
namespace Plugin\Robot;

use DbTable\MusicArtist;
use DbTable\MusicSong;

class Music163 extends \Plugin\Robot
{
    // 参数
    public $site_id = 1;
    public $cats = '1001,1002,1003,2001,2002,2003,4001,4002,4003,6001,6002,6003,7001,7002,7003';

    const URL_ARTIST_PAGE = 0;
    const URL_ARTIST_LIST = 1;

    public $urls = [
        "https://music.163.com/artist?id=%1",
        "https://music.163.com/discover/artist/cat?id=%1&initial=%2"
    ];

    public function _init()
    {
        $this->cache_dir = $cache_dir = CACHE_ROOT . '/https/music.163.com';
        $this->paths = [
            $cache_dir . "/artist/%1.html",
            $cache_dir . "/discover/artist/cat/%1/cat%2.htm",
        ];
    }

    public function downloadList()
    {
        $cats = explode(',', $this->cats);
        $page = $this->attr['page'];
        $cat = $this->attr['cat'];
        if (1 == $page) {
            $page = 0;
        }

        $size = $this->putFileCurl(null, self::URL_ARTIST_LIST, $cat, $page);
        if (!$size) {
            $file = $this->getProp(self::URL_ARTIST_LIST, 'paths', $cat, $page);
            return [
                'code' => 1,
                'msg' => $_SERVER['REQUEST_URI'],
                'info' => [$file, __FILE__, __LINE__],
            ];
        }

        $msg = '';
        if (!$page) {
            $msg = "/robot/music163/download/list?debug&type=json&cat=$cat&page=65";
        } elseif (90 == $page && 7003 > $cat) {
            foreach ($cats as $key => $value) {
                if ($value == $cat) {
                    $key++;
                    $cat = $cats[$key];
                    break;
                }
            }
            $msg = "/robot/music163/download/list?debug&type=json&cat=$cat";
        }

        return [
            'msg' => $msg,
            'result' => $size,
            'pageCount' => 90,
        ];
    }

    public function parseList()
    {
        $cats = explode(',', $this->cats);
        $page = $this->attr['page'];
        $cat = $this->attr['cat'] ?? 1001;
        if (1 == $page) {
            $page = 0;
        }

        $Artist = new MusicArtist;
        $str = $this->getPathContents(self::URL_ARTIST_LIST, $cat, $page);
        $doc = new \DOMDocument('1.0', 'utf-8');
        @$doc->loadHTML($str);
        $li = $doc->getElementsByTagName('li');
        $length = $li->length;

        $arr = [];
        for ($i = 0; $i < $length; $i++) {
            $node = $li->item($i);
            $a = $node->getElementsByTagName('a');
            $len = $a->length;
            $row = [];
            for ($j = 0; $j < $len; $j++) {
                $nd = $a->item($j);
                $href = $nd->getAttribute('href');
                $tt = $nd->nodeValue;
                if (preg_match('/artist\?id=(\d+)/', $href, $matches)) {
                    $row['artist'] = $matches[1];
                    $row['name'] = $tt;
                } elseif (preg_match('/user\/home\?id=(\d+)/', $href, $matches)) {
                    $row['home'] = $matches[1];
                }
            }

            if ($row) {
                // 艺术家
                $row['site'] = $this->site_id;
                $row['status'] = 1;
                $row['cat'] = $cat;
                $row['initial'] = $page;
                $row['exist'] = $Artist->exist($row);
                $arr[] = $row;
            }
        }

        $msg = '';
        if (!$page) {
            $msg = "/robot/music163/parse/list?debug&type=json&cat=$cat&page=65";
        } elseif (90 == $page && 7003 > $cat) {
            foreach ($cats as $key => $value) {
                if ($value == $cat) {
                    $key++;
                    $cat = $cats[$key];
                    break;
                }
            }
            $msg = "/robot/music163/parse/list?debug&type=json&cat=$cat";
        }

        return [
            'msg' => $msg,
            'result' => $arr,
            'pageCount' => 90,
        ];
    }

    public function downloadArtist()
    {
        $size = $this->putFileCurl(null, self::URL_ARTIST_PAGE, $this->attr['page']);
        if (!$size) {
            $file = $this->getProp(self::URL_ARTIST_PAGE, 'paths', $this->attr['page']);
            return [
                'code' => 1,
                'msg' => $_SERVER['REQUEST_URI'],
                'info' => [__FILE__, __LINE__, $file],
            ];
        }

        $list = [];
        $msg = '';

        return [
            'msg' => $msg,
            'result' => $list,
            'pageCount' => $_SESSION['total_page'],
        ];
    }

    public function parseArtist()
    {
        $Artist = new MusicArtist;
        $Song = new MusicSong;

        // 解析 HTML
        $str = $this->getPathContents(self::URL_ARTIST_PAGE, $this->attr['page']);
        $doc = new \DOMDocument('1.0', 'utf-8');
        @$doc->loadHTML($str);
        $ar = $doc->getElementById('artist-name');
        $name = $ar->nodeValue;
        $so = $doc->getElementById('song-list-pre-data');
        $song = $so->nodeValue;
        $arr = json_decode($song);
        $result = [
            'artist' => -1,
            'song' => [],
        ];

        // 热门歌曲
        $i = 1;
        foreach ($arr as $row) {
            $data = [
                'site' => $this->site_id,
                'artist' => $this->attr['page'],
                'album' => $row->album->id,
                'song' => $row->id,
                'top' => $i,
                'name' => $row->name,
                'duration' => $row->duration,
            ];
            $result['song'][] = $exist = $Song->exist($data);
            $i++;
        }

        // 艺术家
        $data = [
            'site' => $this->site_id,
            'artist' => $this->attr['page'],
            'name' => $name,
        ];
        $result['artist'] = $exist = $Artist->exist($data);

        $msg = '';

        return [
            'msg' => $msg,
            'result' => $result,
            'pageCount' => '',
        ];
    }
}
