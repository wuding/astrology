<?php
/**
 * 房天下
 *
 * www.fang.com
 */
namespace Plugin\Robot;

class Fang extends \Plugin\Robot
{
	public $enable_relay = true;
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
		
		/* 解析 */
		$data = $this->getPathContents(2, 'mas');
		$doc = $this->parse_list($data, null, null, 'gbk', ['/charset=gbk/', 'charset=utf-8']);
		
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
		$doc = $this->parse_list($data, 'utf-8');
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
	 * 解析列表DOM
	 * @param  string $str           html
	 * @param  string $charset       html字符集
	 * @param  string $id            元素id
	 * @param  string $from_encoding 源编码
	 * @param  array  $replace       html替换
	 * @return object                dom元素
	 */
	public function parse_list($str = null, $charset = null, $id = null, $from_encoding = null, $replace = [])
	{
		if ($from_encoding) {
			$mb = new \Astrology\Extension\Mbstring($str, $from_encoding);
			if ($replace) {
				$str = $mb->preg_replace($replace[0], $replace[1]);
			} else {
				$str = $mb->str;
			}
		}

		$dom = new \Astrology\Extension\DOM($str, $charset);
		$doc = $dom->doc;
		if ($id) {
			$doc = $doc->getElementById($id);
		}
		return $doc;
	}

	/**
	 * 检测出租列表数据
	 * @param  object $doc dom
	 * @return array       检测结果集
	 */
	public function check_list($doc)
	{
		$detail = new \DbTable\RentingSiteDetail;
		$li = $doc->getElementsByTagName('li');
		$len = $li->length;
		$list = [];
		for ($j = 0; $j < $len; $j++) {
			$nd = $li->item($j);
			$h3 = $nd->getElementsByTagName('h3');
			$data_bg = $nd->getAttribute('data-bg');
			if ($data_bg) {
				$bg = json_decode($data_bg);
				$arr = [
					'site_id' => $this->site_id,
					'title' => $h3->item(0)->nodeValue,
					'item_id' => $bg->houseid,
					'agent_id' => $bg->agentid,
					'type' => $bg->housetype,
					'data' => $bg->listingtype,
				];
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
		$mb = new \Astrology\Extension\Mbstring($str, 'gbk');		
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
		$area = new \DbTable\RentingSiteArea;
		$data = $this->getPathContents();
		
		/*
		$path = $this->getProp(0, 'paths');		
		\Astrology\Extension\Zlib::uncompress($path, $path . '.txt');
		*/
		
		$str = gzdecode($data);
		# header('Content-Type: text/html; charset=utf-8');
		$mb = new \Astrology\Extension\Mbstring($str, 'gbk');		
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
