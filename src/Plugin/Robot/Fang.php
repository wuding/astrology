<?php
/**
 * 房天下
 *
 * www.fang.com
 */
namespace Plugin\Robot;

use Astrology\Extension\Mbstring;
use Astrology\Extension\DOM;
use DbTable\RentingSiteArea;
use DbTable\RentingSiteDetail;


class Fang extends \Plugin\Robot
{
    public $enable_relay = true;
    public $overwrite = false;
    public $api_host = 'http://lan.urlnk.com';
    public $site_id = 1;


    /**
     * 自定义初始化
     * 
     * @return [type] [description]
     */
    public function _init()
    {
        $cache_dir = CACHE_ROOT . '/http/zu.fang.com';
        $this->api_host = 'http://' . $_SERVER['HTTP_HOST'];

        $this->paths = [
            $cache_dir . '/cities.aspx.gz', //城市列表
            $cache_dir . '/%1/house/i3%2.gz', //出租列表pc 1城市 2页码
            $cache_dir . '/m/zf/%1/index.html', //出租首页 1城市
            $cache_dir . '/m/zf/%1/%3/%2.html', //出租详情 1城市 2ID 3类型
            $cache_dir . '/m/zf/%1/%2.html', //出租列表 1城市 2页码
        ];

        $this->urls = [
            'http://zu.fang.com/cities.aspx',
            'http://%1.zu.fang.com/house/i3%2/',
            'https://m.fang.com/zf/%1/',
            'https://m.fang.com/zf/%1/%3_%2.html',
            'https://m.fang.com/zf/?renttype=cz&c=zf&a=ajaxGetList&city=%1&page=%2',
        ];

        $this->relay_urls = [
            'parse/city' => "$this->api_host/robot/fang/parse/city?debug&type=json",
            'download/zf' => "$this->api_host/robot/fang/download/zf?debug&type=json",
            'download/list' => "$this->api_host/robot/fang/download/list?debug&type=json",
        ];
    }

    /*
     +------------------------------
     | 列表
     +------------------------------
     */

    /**
     * 下载列表首页
     * 
     * @return array api数据
     */
    public function downloadZf()
    {
        /* 下载 */
        $size = $this->putFileCurl([], 2, 'mas');
        if (!$size) {
            return [
                'code' => 1, 
                'msg' => 'download error', 
                'info' => [__FILE__, __LINE__],
            ];
        }
        # print_r($size);exit;
        
        /* 解析 */
        $data = $this->getPathContents(2, 'mas');
        $doc = $this->parse_dom($data, null, null, 'gbk', ['/charset=gbk/', 'charset=utf-8'])[0]; # echo exit;
        
        // 页数
        $input = $doc->getElementsByTagName('input');
        $length = $input->length;
        $arr = [];
        for ($i = 0; $i < $length; $i++) {
            $node = $input->item($i);
            $data_id = $node->getAttribute('data-id');
            if (in_array($data_id, ['total', 'pagesize'])) {
                $arr[$data_id] = $node->getAttribute('value');
                if ('pagesize' == $data_id) {
                    break;
                }
            }
        }
        # print_r($arr);exit; 
        $obj = (object) $arr;
        $limit = 16;
        $_SESSION['next_page'] = $next_page = $obj->pagesize / $limit + 1;
        $_SESSION['total_page'] = $total_page = ceil($obj->total / $limit);

        /* 列表 */
        $doc = $doc->getElementById('content');     
        $list = $this->check_list($doc);
        # print_r($list);exit; 
        
        $msg = $this->enable_relay ? $this->relay_urls['download/list'] . "&page=$next_page" : '';
        return [
            'msg' => $msg,
            'result' => $list,
            'pageCount' => 1,
        ];
    }

    

    /**
     * 下载出租列表
     * @return array api数据
     */
    public function downloadList()
    {
        /* 下载 */
        $size = $this->putFileCurl([], 4, 'mas', $this->attr['page']);
        if (!$size) {
            return [
                'code' => 1, 
                'msg' => 'download error', 
                'info' => [__FILE__, __LINE__],
            ];
        }

        /* 检测 */
        $data = $this->getPathContents(4, 'mas', $this->attr['page']);
        $doc = $this->parse_dom($data, 'utf-8')[0]; # echo exit;
        $list = $this->check_list($doc);
        #print_r($list);exit;
        #
        $msg = '';

        return [
            'msg' => $msg,
            'result' => $list,
            'pageCount' => $_SESSION['total_page'],
        ];
    }

    /**
     * 解析 DOM
     * @param  string $str           html
     * @param  string $charset       html字符集
     * @param  string $id            元素id
     * @param  string $from_encoding 源编码
     * @param  array  $replace       html替换
     * @return object                dom元素
     */
    public function parse_dom($str = null, $charset = null, $id = null, $from_encoding = null, $replace = [])
    {
        if ($from_encoding) {
            $mb = new Mbstring($str, $from_encoding);
            if ($replace) {
                $str = $mb->preg_replace($replace[0], $replace[1]);
            } else {
                $str = $mb->str;
            }
        }

        $dom = new DOM($str, $charset);
        $doc = $dom->doc;
        if ($id) {
            $doc = $doc->getElementById($id);
        }
        return [$doc, $str];
    }

    /**
     * 检测出租详情数据
     * @param  object $doc dom对象
     * @param  object $row 数据条目对象
     * @return array       检测结果
     */
    public function check_detail($doc, $row = null)
    {
        $Detail = new RentingSiteDetail;
        $html = $doc[1];
        $doc = $doc[0];
        $dom = new DOM();
        $slider = $doc->getElementById('slider');
        $section = $doc->getElementsByTagName('section');
        $body = $doc->getElementsByTagName('body');
        
       if ($body->length) {
            $class = $body[0]->getAttribute('class');
            if ('box404' == $class) {
                $update = $Detail->update(['cache_set' => 'status=404'], $row->detail_id);
                return [404, $update];
            }
        } else {
            echo $html;
            print_r($doc);
        }
       
        $pic = [];
        if ($slider) {
            $slides = [];
            $img = $slider->getElementsByTagName('img');
            $len = $img->length;
            for ($i = 0; $i < $len; $i++) {
                $node = $img->item($i);
                $src = $node->getAttribute('src');
                $slides[] = $src;
            }
            $pic['slides'] = implode(',', $slides);
        }

        for ($i = 0; $i < $section->length; $i++) {
            $node = $section->item($i);
            $class = $node->getAttribute('class');
            switch ($class) {
                case 'xqCaption mb8':
                    $pic = $this->xqCaption($node, $pic);
                    break;
                case 'xqBox mb8':
                    $pic = $this->xqBox($node, $pic);
                    break;
                case 'mBox':
                    $pic = $this->xqDescription($node, $pic);
                    break;
            }
            $pic[] = $class;
            if ('mBox' == $class) {
                break;
            }
        }
        
        return $arr = [
            'slides' => $pic,
            'row' => $row,
        ];
    }

    public function xqDescription($node, $data = [])
    {
        $div = $node->getElementsByTagName('div');
        $arr = [];
        for ($i = 0; $i < $div->length; $i++) {
            $nd = $div->item($i);
            $class = $nd->getAttribute('class');
            switch ($class) {
                case 'fymsList pdX20':
                    $data['fyms'] = $this->xqFymsList($nd);
                    break;
            }
            $arr[] = $class;
            if ('fymsList pdX20' == $class) {
                break;
            }
        }

        $data[] = $arr;
        return $data;
    }

    public function xqFymsList($node)
    {
        $span = $node->getElementsByTagName('li');
        $arr = [];
        for ($i = 0; $i < $span->length; $i++) {
            $nd = $span->item($i);
            $h3 = $nd->getElementsByTagName('h3')[0];
            $p = $nd->getElementsByTagName('p')[0];
            $arr[trim($h3->nodeValue)] = trim($p->nodeValue);
        }
        return $arr;
    }

    public function xqBox($node, $data = [])
    {
        $div = $node->getElementsByTagName('div');
        $arr = [];
        for ($i = 0; $i < $div->length; $i++) {
            $node = $div->item($i);
            $class = $node->getAttribute('class');
            switch ($class) {
                case 'price-box mt20':
                    $data['price'] = $this->xqPrice($node);
                    break;
                case 'bb pdY10':
                    $data['table'] = $this->xqTable($node);
                    break;
                case 'ptss-zf pdY14':
                    $data['facility'] = $this->xqFacility($node);
                    goto a;
                    break;
            }
            $arr[] = $class;
        }
        a:
        $data[] = $arr;
        return $data;
    }

    public function xqCaption($node, $data = [])
    {
        $p = $node->getElementsByTagName('p');
        $arr = [];
        for ($i = 0; $i < $p->length; $i++) {
            if (1 < $i) {
                break;
            }

            $nd = $p->item($i);
            switch ($i) {
                case 0:
                    $a = $nd->getElementsByTagName('a');
                    $data['crumbs'] = $this->xqCrumbs($a);
                    break;
                case 1:
                    $data['refresh_time'] = preg_replace('/刷新时间：\s+/', '', trim($nd->nodeValue));
                    break;
            }
        }
        return $data;
    }

    public function xqCrumbs($a)
    {
        $arr = [];
        for ($i = 0; $i < $a->length; $i++) {
            $node = $a->item($i);
            $href = $node->getAttribute('href');
            if (preg_match('/\/\/m\.fang\.com\/zf\/([a-z]+)_([a-z0-9_]+)\//', $href, $matches)) {
                $arr[$matches[2]] = $node->nodeValue;
            }
        }
        return $arr;
    }

    public function xqPrice($node)
    {
        $span = $node->getElementsByTagName('span');
        $arr = [];
        for ($i = 0; $i < $span->length; $i++) {
            $nd = $span->item($i);
            $arr[] = $nd->nodeValue;
        }
        return $arr;
    }

    public function xqTable($node)
    {
        $span = $node->getElementsByTagName('li');
        $arr = [];
        for ($i = 0; $i < $span->length; $i++) {
            $nd = $span->item($i);
            $arr[] = explode('：', trim($nd->nodeValue));
        }
        return $arr;
    }

    public function xqFacility($node)
    {
        $span = $node->getElementsByTagName('span');
        $arr = [];
        for ($i = 0; $i < $span->length; $i++) {
            $nd = $span->item($i);
            $class = $nd->getAttribute('class');
            $arr[$nd->nodeValue] = $class;
        }
        return $arr;
    }

    /**
     * 检测出租列表数据
     * @param  object $doc dom
     * @return array       检测结果集
     */
    public function check_list($doc)
    {
        $detail = new RentingSiteDetail;
        $dom = new DOM();
        $li = $doc->getElementsByTagName('li');
        $len = $li->length;
        $list = [];
        for ($j = 0; $j < $len; $j++) {
            $nd = $li->item($j);            
            $data_bg = $nd->getAttribute('data-bg');
            if ($data_bg) {
                $h3 = $nd->getElementsByTagName('h3');              
                $bg = json_decode($data_bg);
                $img = $nd->getElementsByTagName('img')->item(0)->getAttribute('data-original');
                $span = $nd->getElementsByTagName('span');
                $p = $nd->getElementsByTagName('p');
                $arr = [
                    'site_id' => $this->site_id,
                    'title' => trim($h3->item(0)->nodeValue),
                    'item_id' => $bg->houseid,
                    'agent_id' => $bg->agentid,
                    'type' => $bg->housetype,
                    'data' => $bg->listingtype,
                    'pic' => $img,
                ];
                $tags = [];
                for ($s = 0; $s < $span->length; $s++) {
                    $node = $span->item($s);
                    $class = $node->getAttribute('class');
                    $value = $node->nodeValue;
                    switch ($class) {
                        case 'new':
                            $arr['rental_price'] = $value;
                            break;
                        case 'flor':
                            $arr['refresh_time'] = $value;
                            break;
                        case 'red-z':
                            $tags[] = $value;
                            break;
                        default:
                            break;
                    }
                }
                if ($tags) {
                    $arr['tags'] = implode(',', $tags);
                }

                for ($i = 0; $i < $p->length; $i++) {
                    if (1 < $i) {
                        break;
                    }

                    $nod = $p->item($i);
                    $html = $dom->innerHTML($nod);
                    $text = $dom->stripTagsContent($html);
                    $split = preg_split('/(\s+\-\s+|\s+)/', $text, 2);
                    # print_r([$text, $split]);
                    switch ($i) {
                        case 0:
                            $arr['house_type'] = $split[0];
                            $arr['rental_method'] = isset($split[1]) ? $split[1] : '';
                            break;
                        case 1:
                            list($arr['district_name'], $arr['complex_name']) = $split;
                            break;                          
                    }
                }
                # print_r($arr);exit;
                $list[] = $detail->exist($arr);
            }
        }
        return $list;
    }

    /**
     * 下载PC版列表
     * 
     * @return [type] [description]
     */
    public function downloadPc()
    {
        /*      
        $size = $this->putFile(1, 'mas', $this->attr['page']);
        if (!$size) {
            return [
                'code' => 1, 
                'msg' => 'download error', 
                'info' => [__FILE__, __LINE__],
            ];
        }
        */

        $data = $this->getPathContents(1, 'mas', $this->attr['page']);
        $str = gzdecode($data);
        # header('Content-Type: text/html; charset=utf-8');
        $mb = new Mbstring($str, 'gbk');        
        echo $str = $mb->preg_replace('/charset=gb2312/', 'charset=utf-8');exit;

        $msg = '';

        return [
            'msg' => $msg,
            'result' => $size,
            'pageCount' => 1,
        ];
    }

    /*
     +------------------------------
     | 出租详情
     +------------------------------
     */

    /**
     * 下载出租详情
     * @return array api数据
     */
    public function downloadDetail()
    {
        # header('Content-Type: text/html; charset=utf-8');
        $this->min_size = 200;
        $this->returnVars = ['data', 'filesize']; # 
        $page = $this->attr['page'];
        $limit = 10;
        $Detail = new RentingSiteDetail;
        $where = "status IN (-1,-2)";       
        $all = $Detail->fetchAll($where, 'detail_id,city_name,item_id,type', 'detail_id', $page, $limit);
        $pageCount = $Detail->pageCount($where, $limit);
        
        $result = [];
        foreach ($all as $row) {
            $put = $this->putFileCurl([], 3, $row->city_name, $row->item_id, $row->type);
            # $put = 0; 
            if (!$put) {
                return [
                    'code' => 1, 
                    'msg' => 'download error', 
                    'info' => [__FILE__, __LINE__],
                    'row' => $row,
                ];
            }
            /*
            print_r($put);exit;
            
            echo $put['data'];exit;
             */
            $doc = $this->parse_dom($put['data'], null, null, 'gbk', [['/charset=\"gbk\"/', '/charset=gbk/'], ['charset="utf-8"', 'charset=utf-8']]); # echo $doc[1];exit;
            
            $put = $this->check_detail($doc, $row);
            $result[] = $put;
        }

        $msg = '';
        return [
            'msg' => $msg,
            'result' => $result,
            'pageCount' => $pageCount,
        ];
    }

    /*
     +------------------------------
     | 地点区域
     +------------------------------
     */

    /**
     * 下载城市列表
     * @return array api数据
     */
    public function downloadCity()
    {
        $size = $this->putFile();
        if (!$size) {
            return [
                'code' => 1, 
                'msg' => 'download error', 
                'info' => [__FILE__, __LINE__],
            ];
        }

        $msg = $this->enable_relay ? $this->relay_urls['parse/city'] : '';
        return [
            'msg' => $msg,
            'result' => $size,
            'pageCount' => 1,
        ];
    }

    /**
     * 解析城市列表
     * @return array api数据
     */
    public function parseCity()
    {
        $area = new RentingSiteArea;
        $data = $this->getPathContents();
        
        /*
        $path = $this->getProp(0, 'paths');     
        \Astrology\Extension\Zlib::uncompress($path, $path . '.txt');
        */
        
        $str = gzdecode($data);
        # header('Content-Type: text/html; charset=utf-8');
        $mb = new Mbstring($str, 'gbk');        
        $str = $mb->preg_replace('/charset=gb2312/', 'charset=utf-8');
        $doc = new \DOMDocument('1.0', 'utf-8');
        @$doc->loadHTML($str);      
        $c02 = $doc->getElementById('c02');

        // 省
        $li = $c02->getElementsByTagName('li');
        $len = $li->length;
        $last = $len - 1;
        $arr = [];
        for ($i = 0; $i < $len; $i++) {
            $node = $li->item($i);
            $strong = $node->getElementsByTagName('strong');
            $a = $node->getElementsByTagName('a');
            $prov = $i;
            if (0 == $i) { //直辖市
                
            } elseif ($last == $i) { //其他
                $prov = 0;
            } else { //省市自治区
                $prov = $strong->item(0)->nodeValue;
                $prov = $area->provinceExists($prov, $this->site_id);
            }

            // 市
            $length = $a->length;
            $cities = [];           
            for ($j = 0; $j < $length; $j++) {
                $nd = $a->item($j);
                $spell = $nd->getAttribute('spell');
                $href = $nd->getAttribute('href');
                $abbr = '';
                if (preg_match('/^(http:|)\/\/([a-z]+\.zu|zu\.[a-z]+)\.fang\.com/i', $href, $matches)) {
                    # print_r($matches);
                    $abbr = preg_replace('/^zu\.|\.zu$/i', '', $matches[2]);
                }

                $ct = [
                    'site_id' => $this->site_id,
                    'upper_id' => $prov,
                    'title' => $nd->nodeValue,
                ];
                $set = [
                    'name' => $spell,
                    'abbr' => $abbr,
                ];
                $city = $area->cityExists($ct, $set, 'area_id');
                $cities[$city] = $ct + $set;
            }
            $arr[] = [$prov, $cities];
            
        }
        # print_r($arr);
        
        $msg = $this->enable_relay ? $this->relay_urls['download/zf'] : '';
        return [
            'msg' => $msg,
            'result' => $arr,
            'pageCount' => 1,
        ];
    }
}
