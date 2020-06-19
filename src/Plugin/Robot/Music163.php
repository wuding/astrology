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
use DbTable\MusicSiteLyric;
use DbTable\MusicSiteAudio;
use DbTable\MusicSiteAudioUrl;
use Metowolf\Meting;

class Music163 extends \Plugin\Robot
{
    // 参数
    public $site_id = 1;
    public $cats = '1001,1002,1003,2001,2002,2003,4001,4002,4003,6001,6002,6003,7001,7002,7003';
    public $api_cookie = 'os=pc; osver=Microsoft-Windows-10-Professional-build-10586-64bit; appver=2.0.3.131777; channel=netease; __remember_me=true';

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
        $page = $this->attr['page'];
        $Artist = new MusicArtist;

        // 获取总量和艺术家 ID
        $count = $Artist->count("site = $this->site_id");
        $row = $Artist->offset($page - 1, $this->site_id);
        $artistId = $row->artist;

        // 下载
        $size = $this->putFileCurl(null, self::URL_ARTIST_PAGE, $artistId);
        if (!$size) {
            $file = $this->getProp(self::URL_ARTIST_PAGE, 'paths', $artistId);
            return [
                'code' => 1,
                'msg' => $_SERVER['REQUEST_URI'],
                'info' => [__FILE__, __LINE__, $file],
            ];
        }

        $msg = '';
        return [
            'msg' => $msg,
            'result' => $size,
            'pageCount' => $count,
        ];
    }

    public function parseArtist()
    {
        $page = $this->attr['page'];
        $Artist = new MusicArtist;
        $Song = new MusicSong;
        $arr = null;
        $time = time();

        // 处理结果
        $result = [
            'artist' => -1,
            'song' => [],
        ];

        // 获取总量和艺术家 ID
        $count = $Artist->count("site = $this->site_id");
        $row = $Artist->offset($page - 1, $this->site_id);
        $artistId = $row->artist;

        // 解析 HTML
        $str = $this->getPathContents(self::URL_ARTIST_PAGE, $artistId);
        if ('0' === $str) {
            $data = [
                'updated' => $time,
                'status' => -3,
            ];
            $result['update'] = $Artist->update($data, $artistId);
            goto __END__;
        }
        $doc = new \DOMDocument('1.0', 'utf-8');
        @$doc->loadHTML($str);

        // 艺术家名称
        $ar = $doc->getElementById('artist-name');
        if (null === $ar) {
            if (preg_match('/<h2 id=\"artist-name\" ([^<\/]+)>(.*)<\/h2>/', $str, $matches)) {
                $name = $matches[2];
            }
        } else {
            $name = $ar->nodeValue;
        }

        // 歌曲列表
        $so = $doc->getElementById('song-list-pre-data');
        if ($so) {
            $song = $so->nodeValue;
            $arr = json_decode($song);
        }
        if (!is_array($arr)) {
            $filename = $this->cache_dir . '/artist.json';
            if (preg_match('/<textarea id=\"song-list-pre-data\" style=\"display:none;\">(.*)<\/textarea>/', $str, $matches)) {
                $put = file_put_contents($filename, $matches[1]);
            } else {
                goto __AR__;
            }
            $son = file_get_contents($filename);
            $arr = json_decode($son);
            $unlink = unlink($filename);
            if (!is_array($arr)) {
                print_r(array($filename, $artistId, __FILE__, __LINE__));
                exit;
            }
        }

        // 调试
        if (isset($_GET['debug']) && 'test' == $_GET['debug']) {
            print_r([$arr, __FILE__, __LINE__]);
            exit;
        }

        // 热门歌曲
        $i = 1;
        foreach ($arr as $row) {
            $pieces = [];
            foreach ($row->artists as $value) {
                $pieces[] = $value->id;
            }
            $alias = json_encode($row->alias);

            $data = [
                'site' => $this->site_id,
                'artists' => implode(',', $pieces),
                'album' => $row->album->id,
                'song' => $row->id,
                'top' => $i,
                'name' => $row->name,
                'duration' => $row->duration,
                'mv' => $row->mvid,
                'alias' => $alias,
            ];
            $result['song'][] = $exist = $Song->exist($data);

            // 探测
            /*
            copyrightId 0 -1
            status -1 0 1 3
            fee 0 8
            score 100 95
            transNames []
            */
            $string = 'ftype,publishTime,djid,type';
            $variable = explode(',', $string);
            $val = null;
            foreach ($variable as $value) {
                if ($row->$value) {
                    $val = $value;
                }
            }
            if ($val) {
                print_r(array($val, $row, __FILE__, __LINE__));
                exit;
            }

            $i++;
        }

        __AR__:
        // 艺术家
        $data = [
            'site' => $this->site_id,
            'artist' => $artistId,
            'name' => $name,
        ];
        $result['artist'] = $exist = $Artist->exist($data);

        __END__:
        $msg = '';
        return [
            'msg' => $msg,
            'result' => $result,
            'pageCount' => $count,
        ];
    }

    public function downloadLyric()
    {
        $page = $this->attr['page'];
        $Song = new MusicSong;
        $Lyric = new MusicSiteLyric;
        $api = new Meting('netease');
        $result = [];
        if ($this->api_cookie) {
            $api->cookie($this->api_cookie);
        }

        // 获取总量和歌曲 ID
        $count = $Song->count("site = $this->site_id");
        $row = $Song->offset($page - 1, $this->site_id);
        $songId = $row->song;
        $json = $api->lyric($songId);
        $obj = json_decode($json);

        // 属性检测
        if (isset($obj->nolyric)) {
            if (1 == $obj->nolyric) {
                goto __END__;
            }
            print_r(array($obj, __FILE__, __LINE__));
            exit;
        }
        if (!isset($obj->klyric)) {
            $obj->klyric = new \stdClass;
        }
        if (!isset($obj->tlyric)) {
            $obj->tlyric = new \stdClass;
        }
        if (!isset($obj->klyric->lyric)) {
            $obj->klyric->lyric = null;
        }
        if (!isset($obj->tlyric->lyric)) {
            $obj->tlyric->lyric = null;
        }

        // 写入
        if (isset($obj->lrc) && $obj->lrc->lyric) {
            $result['lrc'] = $this->lyric($songId, $obj->lrc, 1);
        }
        if ($obj->klyric->lyric) {
            $result['klyric'] = $this->lyric($songId, $obj->klyric, 2);
        }
        if ($obj->tlyric->lyric) {
            $result['tlyric'] = $this->lyric($songId, $obj->tlyric, 3);
        }

        __END__:
        $msg = '';
        return [
            'msg' => $msg,
            'result' => $result,
            'pageCount' => $count,
        ];
    }

    public function lyric($songId, $obj, $type = 0)
    {
        if (!trim($obj->lyric)) {
            return -1;
        }
        $arr = ['_', 'lrc', 'xml', 'txt'];
        $ext = $arr[$type];
        $Lyric = new MusicSiteLyric;
        $filename = "$this->cache_dir/lyric/$songId-$obj->version.$ext";
        $put = file_put_contents($filename, $obj->lyric);
        if (false === $put) {
            print_r(array($filename, $put, $obj, __FILE__, __LINE__));
            exit;
        }

        // 检测
        $data = [
            'site' => $this->site_id,
            'song' => $songId,
            'version' => $obj->version,
            'size' => $put,
            'type' => $type,
        ];
        return $Lyric->exist($data);
    }

    public function downloadAudio()
    {
        $page = $this->attr['page'];
        $Audio = new MusicSiteAudio;
        $Url = new MusicSiteAudioUrl;
        $Song = new MusicSong;
        $api = new Meting('netease');
        if ($this->api_cookie) {
            $api->cookie($this->api_cookie);
        }

        // 获取总量和歌曲 ID
        $count = $Song->count("site = $this->site_id");
        $row = $Song->offset($page - 1, $this->site_id);
        $songId = $row->song;
        $result = ['song' => $songId];

        // 主要
        $u = $Song->get(array('site' => $this->site_id, 'song' => $songId), 'download');
        if ($download = $u->download) {
            goto __END__;
        }
        $json = $api->url($songId);
        $row = json_decode($json);
        $md5 = md5($json);

        // 日志
        $filename = "$this->cache_dir/logs/url/$songId.json";
        $contents = @file_get_contents($filename);
        if (false === $contents) {
            $arr = [];
            $arr[$md5] = $row;
            $data = json_encode($arr);
            $put = file_put_contents($filename, $data);

        } else {
            $log = (array) json_decode($contents);
            if (!isset($log[$md5])) {
                $log[$md5] = $row;
                $data = json_encode($log);
                $put = file_put_contents($filename, $data);
            }
        }

        // 写入
        $arr = [];
        $download = 0;
        foreach ($row->data as $key => $value) {
            $exist = $urlId = $up = $update = $dl = null;
            if (!$value->url) {
                goto __LOG__;
            }

            // 音频
            $data = [
                'site' => $this->site_id,
                'song' => $songId,
                'md5' => $value->md5,
                'size' => $value->size,
                'br' => $value->br,
            ];
            $exist = $Audio->exist($data, true);
            if (is_object($exist)) {
                if (1 == $exist->status) {
                    goto __LOG__;
                }

            } elseif (!is_numeric($exist) || !$exist) {
                var_dump($exist);
                print_r(array($data, __FILE__, __LINE__));
                exit;
            }

            // 地址
            $dat = [
                'audio' => $exist,
                'url' => $value->url,
            ];
            $urlId = $Url->exist($dat);
            if (!is_numeric($urlId) || !$urlId) {
                var_dump($urlId);
                print_r(array($dat, __FILE__, __LINE__));
                exit;
            }

            // 下载
            $dl = $this->download($value->url, $songId, $exist, $urlId);
            $size = $dl['put'];
            $status = $size ? 1 : -3;
            if ($size) {
                $download++;
            }
            $up = $Url->update(
                array('status' => $status, 'size' => $size, 'extension' => $dl['ext']),
                $urlId
            );
            $update = $Audio->update(
                array('status' => $status),
                $exist
            );

            // 记录
            __LOG__:
            $arr[] = array(
                'audio' => $exist, 'url' => $urlId,
                'up' => $up, 'update' => $update, 'download' => $dl
            );
        }
        $result['log'] = $arr;

        // 更新
        $u = $Song->update(
            array('download' => $download ? : -1),
            array('site' => $this->site_id, 'song' => $songId)
        );

        __END__:
        $result['download'] = array('download' => $download, 'update' => $u);

        $msg = '';
        return [
            'msg' => $msg,
            'result' => $result,
            'pageCount' => $count,
        ];
    }

    public function download($url, $song, $au, $ur)
    {
        $path = parse_url($url, PHP_URL_PATH);
        $ext = pathinfo($path, PATHINFO_EXTENSION);
        $filename = "$this->cache_dir/audio/$song-$au-$ur.$ext";
        $data = file_get_contents($url);
        $put = file_put_contents($filename, $data);
        return array('put' => $put, 'ext' => $ext, 'url' => $url, 'filename' => $filename);
    }
}
