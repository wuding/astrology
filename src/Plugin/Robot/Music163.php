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

    const URL_ARTIST_PAGE = 0;

    public $urls = [
        "https://music.163.com/artist?id=%1"
    ];

    public function _init()
    {
        $this->cache_dir = $cache_dir = CACHE_ROOT . '/https/music.163.com';
        $this->paths = [
            $cache_dir . "/artist/%1.html",
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
