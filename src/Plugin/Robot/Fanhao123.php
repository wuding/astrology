<?php
/**
 * 番号大全-番号之家
 * http://www.fanhao123.org
 */
namespace Plugin\Robot;

class Fanhao123 extends \Plugin\Robot
{
	# public $func_format = 'json';
	public $site_id = 42;
	
	public function __construct($arg = null)
	{
		parent::__construct($arg);
		$this->cache_dir = $this->cache_root . '/www.fanhao123.org/';
		
		$this->urls = [
			'http://www.fanhao123.org/L/nvyou%1.html',
			'http://www.fanhao123.org/k/%1_%2.html'
		];
		
		$this->paths = [
			$this->cache_dir . 'L/nvyou%1.html',
			$this->cache_dir . 'k/%1_%2.html',
		];
	}
	
	/**
	 * 解析列表
	 */
	public function parseList()
	{
		$VideoCollect = new \DbTable\PornStar();
		$key = $this->_url_list_key;
		# $filename = $this->getProp($key, 'paths', $this->attr['page']);
		$data = $this->getPathContents($key, $this->attr['page']);
		$source = preg_replace('/charset=gb2312/i', 'charset=utf-8', $data);
		$str = mb_convert_encoding($source, 'utf-8', 'gbk');
		$doc = new \DOMDocument('1.0', 'utf-8');
        @$doc->loadHTML($str);
		
		$div = $doc->getElementsByTagName('div');
		for ($i = 0; $i < $div->length; $i ++) {
			$node = $div->item($i);
			$class = $node->getAttribute('class');
			if ('list' == $class) {
				# echo $i . PHP_EOL;
				break;
			}
		}
		$li = $node->getElementsByTagName('li');
		$arr = [];
		for ($j = 0; $j < $li->length; $j ++) {
			$node = $li->item($j);
			$img = $node->getElementsByTagName('img');
			$p = $node->getElementsByTagName('p');
			$a = $p[0]->getElementsByTagName('a');
			$href = $a[0]->getAttribute('href');
			$text = $a[0]->nodeValue;
			$src = $img[0]->getAttribute('src');
			if (preg_match('/\/([a-z]+)_(\d+)\./i', $href, $matches)) {
				# print_r($matches);
				# $arr[] = [$matches[1], $matches[2], $text, $src];
				$data = [
					'site_id' => $this->site_id,
					'detail_id' => $matches[2],
					'name' => $text,
					'pinyin' => $matches[1],
					'pic' => $src,
				];
				$arr[] = $VideoCollect->check($data);
			} else {
				echo $text. PHP_EOL;
				echo $href . PHP_EOL;
				exit;
			}
		}
		return ['result' => $arr];
	}
	
	/**
	 * 下载详情
	 */
	public function downloadDetail()
	{
		$pageSize = 10;
		$offset = $this->attr['page'] * $pageSize - $pageSize;
		$VideoCollect = new \DbTable\PornStar();
		$ent = $VideoCollect->select(null, 'collect_id,detail_id,pinyin', 'collect_id', "$offset,$pageSize");
		$pageCount = ceil($VideoCollect->count() / $pageSize);
		$result = [];
		$key = 1;
		foreach ($ent as $en) {
			$result[] = $this->putFile($key, $en->pinyin, $en->detail_id);
		}
		return ['result' => $result, 'pageCount' => $pageCount];
	}
	
	/**
	 * 解析详情
	 */
	public function parseDetail()
	{
		$pageSize = 5;
		$offset = $this->attr['page'] * $pageSize - $pageSize;
		$VideoCollect = new \DbTable\PornStar();
		$PornFilm = new \DbTable\PornFilm();
		$ent = $VideoCollect->select(null, 'collect_id,detail_id,pinyin', 'collect_id', "$offset,$pageSize");
		$pageCount = ceil($VideoCollect->count() / $pageSize);
		$result = [];
		$key = 1;
		foreach ($ent as $en) {
			$data = $this->getPathContents($key, $en->pinyin, $en->detail_id);
			$source = preg_replace('/charset=gb2312/i', 'charset=utf-8', $data);
			$str = mb_convert_encoding($source, 'utf-8', 'gbk');
			$array = $this->tbody($str);
			
			$shift = array_shift($array);
			
			
			$_0 = $_2 = 'null';
			$_1 = $shift['作品名称'];
			if (isset($shift['番号'])) {
				$_0 = $shift['番号'];
			} else {
				print_r($shift);
				print_r($array);
				exit;
			}
			if (isset($shift['片长'])) {
				$_2 = $shift['片长'];
			}
			$_3 = $shift['出版日期'];
			$_4 = $shift['发行商'];
			
			$row = [];
			$no = 0;
			foreach ($array as $arr) {
				if ('null' != $_2 && 5 > count($arr)) {
					print_r([$en, $no, $arr]);
					print_r($array);
					exit;
				} elseif (4 > count($arr)) {
					print_r([$en, $no, $arr]);
					print_r($array);
					exit;
				}
				$arr['null'] = '';
				$data = [
					'site_id' => $this->site_id,
					'collect_ids' => $en->collect_id,				
					'number' => $arr[$_0],
					'name' => $arr[$_1],
					'length' => $arr[$_2],
					'released' => $arr[$_3],
					'studio' => $arr[$_4],
				];
				# print_r($data);
				# exit;
				$row[] = $PornFilm->check($data);
				$no++;
			}
			$result[] = $row;
		}
		# print_r($result);
		# exit;
		return ['result' => $result, 'pageCount' => $pageCount];
	}
	
	public function tbody($str)
	{
		$doc = new \DOMDocument('1.0', 'utf-8');
        @$doc->loadHTML($str);
		
		$thead = $doc->getElementsByTagName('thead');
		if (!$thead->length) {
			echo $str;
			exit;
		}
		
		$th = $thead[0]->getElementsByTagName('th');
		$h = [];
		for ($i = 0; $i < $th->length; $i ++) {
			$h[$i] = trim($th[$i]->nodeValue);
		}
		$col = [
			'番号' => '',
			'作品名称' => '',
			'片长' => '',
			'出版日期' => '',
			'发行商' => '',
		];
		$h = array_flip($h);
		# print_r($h);
		# exit;
		
		$tbody = $doc->getElementsByTagName('tbody');
		$tr = $tbody[0]->getElementsByTagName('tr');
		$data = [$h];
		for ($j = 0; $j < $tr->length; $j ++) {
			$node = $tr->item($j);
			$td = $node->getElementsByTagName('td');
			$row = [];
			for ($i = 0; $i < $td->length; $i ++) {
				$row[] = $td[$i]->nodeValue;
			}
			$data[] = $row;
		}
		return $data;
	}
}

