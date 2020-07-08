<?php
/**
 * 酷我音乐
 *
 * @author     Benny Wu
 * @since      2020-5-8
 */
namespace Plugin\Robot;

use Ext\Mbstring;
use DbTable\MusicArtist;
use DbTable\MusicSong;

class Kuwo extends \Plugin\Robot
{
    // 参数
    public $site_id = 2;

    const URL_ARTIST_PAGE = 0;
    const URL_ARTIST_MUSIC = 1;
    const URL_ARTIST_INFO = 2;

    public $urls = [
        "http://bd.kuwo.cn/singer_detail/%1",
        "http://bd.kuwo.cn/api/www/artist/artistMusic?artistid=%1&pn=1&rn=2000&httpsStatus=1&reqId=",
        "http://kuwo.cn/api/www/artist/artistInfo?category=0&prefix=%1&pn=%2&rn=100&httpsStatus=1&reqId="
    ];

    public function _init()
    {
        $this->cache_dir = $cache_dir = CACHE_ROOT . '/https/kuwo.cn';
        $this->paths = [
            $cache_dir . "/artist/%1.html",
            $cache_dir . "/artist_music/%1.json",
            $cache_dir . "/artist_info/%1-%2.json",
        ];
    }

    public function downloadInfo()
    {
        $http_header = ['X-HTTP-Method-Override: GET'];
        $http_header[] = 'Cookie: kw_token=IN5AILKBNE8';
        $http_header[] = 'csrf: IN5AILKBNE8';
        echo $size = $this->putFileCurl($http_header, self::URL_ARTIST_INFO, 'B', $this->attr['page']);
        # echo $u = $this->getProp(self::URL_ARTIST_INFO, 'urls', 'A', $this->attr['page']);
    }

    public function downloadMusic()
    {
        $http_header = ['X-HTTP-Method-Override: GET'];
        $http_header[] = 'Cookie: kw_token=IN5AILKBNE8';
        $http_header[] = 'csrf: IN5AILKBNE8';
        echo $size = $this->putFileCurl($http_header, self::URL_ARTIST_MUSIC, $this->attr['page']);
        echo $u = $this->getProp(self::URL_ARTIST_MUSIC, 'urls', $this->attr['page']);
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
        $mb = new Mbstring($str, 'utf-8');        
        $str = $mb->preg_replace('/<head>/', '<head><meta charset="utf-8">');
        $doc = new \DOMDocument('1.0', 'utf-8');
        @$doc->loadHTML($str);
        $ar = $doc->getElementsByTagName('h1');
        $name = $ar->item(0)->nodeValue;
        $so = $doc->getElementById('song');
        $li = $so->getElementsByTagName('li');
        $length = $li->length;
        $arr = [];
        for ($i = 0; $i < $length; $i++) {
            $node = $li->item($i);
            $a = $node->getElementsByTagName('a');
            $nd = $a->item(0);
            $href = $nd->getAttribute('href');
            $tt = $nd->nodeValue;
            $exp = explode('/', $href);
            $row = [
                'id' => $exp[2],
                'name' => $tt,
            ];
            $arr[] = (object) $row;
        }
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
                'song' => $row->id,
                'top' => $i,
                'name' => $row->name,
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
