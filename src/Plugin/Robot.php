<?php

namespace Plugin;

use Astrology\Extension\Filesystem;
use Astrology\Extension\SimpleXML;
use DbTable\VideoCollect;
use Astrology\Database;

class Robot
{
	public $func_format = '';
	public $page_reverse = false;
	public $cache_root = 'D:\aries\cache\http';
	public $urls = [];
	public $paths = [];
	public $_url_list_key = 0;
	public $attr = [
		'page' => 1,
	];
	
	public function __construct($arg = null)
	{
		if ($arg) {
			$this->init($arg);
		}
	}
	
	public function __call($name, $arguments)
	{
		return [$name, $arguments, __METHOD__, __FILE__, __LINE__];
	}
	
	/**
	 * 初始化
	 *
	 * 执行时需要的属性参数
	 */
	public function init($arg = [])
	{
		$this->attr = array_merge($this->attr, $arg);
	}
	
	/**
	 * 下载列表
	 */
	public function downloadList()
	{
		$key = $this->_url_list_key;
		$result = $this->putFile($key, $this->attr['page']);
		return ['result' => $result];
	}
	
	/**
	 * 解析列表
	 */
	public function parseList()
	{
		$VideoCollect = new VideoCollect(['user' => 'root', 'password' => 'root']);
		$key = $this->_url_list_key;
		$data = $this->getPathContents($key, $this->attr['page']);
		$rss = $this->getSimpleXMLElement($data);
		$video = [];
		if (is_object($rss)) {
			$video = $rss->list[0]->children();
			
		} elseif (is_array($rss)) {
			print_r([$rss, __FILE__, __LINE__]);
			exit;
		}
		
		$arr = [];
		foreach ($video as $r) {
			$data = $this->xmlData($r);
			$data['site_id'] = $this->site_id;
			$arr[] = $VideoCollect->check($data);
		}
		return ['result' => $arr];
	}
	
	/**
	 * 解析分类
	 */
	public function parseCategoryArray()
	{
		$cat = new \DbTable\VideoCategory;
		$class = $this->classes;
		$result = [];
		foreach ($class as $key => $value) {
			if (!is_numeric($value)) {
				$arr = [
					'site_id' => $this->site_id,
					'identification' => $key,
					'name' => $value,
				];
				$result[] = $cat->check($arr);
			}
		}
		
		return [
			'result' => $result,
			'pageCount' => 1,
		];
	}
	
	
	public function bindList()
	{
		$db = new Database(['db_name' => 'xyz_yingmi', 'table_name' => 'view_video_collect']);
		$entry = new \DbTable\VideoEntry;
		$offset = $this->attr['page'] * 10 - 10;
		$all = $db->select(null, 'collect_id,name,class_id,entry_id', 'collect_id', "$offset,10");# 'entry_id = 0'
		$db->table_name = 'video_collect';
		$arr = [];
		foreach ($all as $row) {
			if (!$row->entry_id) {
				$entry_id = $entry->check(['name' => $row->name, 'category_id' => $row->class_id]);
				$arr[] = $db->update(['entry_id' => $entry_id], ['collect_id' => $row->collect_id]);
			}
		}
		return $arr;
	}
	
	public function optimizeList()
	{
		$db = new Database(['db_name' => 'xyz_yingmi', 'table_name' => 'video_collect']);
		$url = new \DbTable\VideoUrl;
		$list = new \DbTable\VideoList;
		$offset = $this->attr['page'] * 10 - 10;
		$all = $db->select(null, 'collect_id,url,entry_id', 'collect_id', "$offset,10");
		$arr = [];
		foreach ($all as $row) {
			if ($row->entry_id) {
				$str = $this->decodeUrl($row->url, $row->collect_id);
				# print_r($str);
				foreach ($str as $s) {
					$url_id = $url->check($s);
					$list_id = $list->check(['entry_id' => $row->entry_id, 'url_id' => $url_id]);
					$arr[] = $list_id;
				}
			}
		}
		return $arr;
	}
	
	public function decodeUrl($url, $entry_id)
	{
		
		$url = trim($url);
		$list = preg_split('/\r\n/', $url);
		$arr = [];
		foreach ($list as $row) {
			$row = trim($row);
			if ($row) {
				$col = preg_split('/#/', $row);
				foreach ($col as $r) {
					$str = preg_split('/(\$|http:)/i', $r);
					$name = $str[0];
					$idx = 2;
					$u = $str[1];
					if (!$u) {
						$u = $str[2];
						$idx++;
					}
					$type = $str[$idx];
					$arr[] = [
						'name' => $name,
						'url' => 'http:' . $u,
						'type' => $type,
					];
				}
			}
		}
		return $arr;
	}
	
	public function xmlData($r)
	{
		$arr = $url = $tags = [];
		$dd = $r->dl->children();
		foreach ($dd as $d) {
			$url[] = (string) $d;
		}
		$arr['url'] = implode(PHP_EOL, $url);
		$arr['description'] = strip_tags((string) $r->des, '<img><a>');
		
		$table = [
			'last' => 'modified',
			'id' => 'detail_id',
			'tid' => 'category_id',
			'name' => 1,
			'type' => 'category_name',
			'pic' => 'poster',
			'lang' => 'language',
			'area' => 1,
			'year' => 1,
			'state' => 'detail_status',
			'note' => 1,
			'actor' => 1,
			'director' => 1,
			'dl' => 0,
			'des' => 0,
		];
		$keys = array_keys($table);
		
		$tag = $r->children();
		foreach ($tag as $t) {
			$n = $t->getName();
			if (!in_array($n, $keys)) {
				print_r([$tags, __FILE__, __LINE__]);
				exit;
			}
			
			$field = $table[$n];
			if (0 !== $field) {
				if (1 === $field) {
					$field = $n;
				}
				$arr[$field] = (string) $r->$n;
				$tags[$n] = $field;
			}
		}
		
		return $arr;
	}
	
	/**
	 * 获取属性配置
	 */
	public function getProp($key = 0, $property = 'urls')
	{
		if (!isset($this->{$property}[$key])) {
			return false;
		}
		$tpl = $this->{$property}[$key];
        for ($i = 2; $i < func_num_args(); $i++) {
            $arg = func_get_arg($i);
			$j = $i - 1;
            $tpl = preg_replace("/%$j/", $arg, $tpl);
        }
        return $tpl;
	}
	
	/**
	 * 写入本地文件
	 */
	public function putFile($key = 0, $_1 = null)
	{
		$file = $this->getProp($key, 'paths', $_1);
		$data = $this->getUrlContents($key, $_1);
		return $size = Filesystem::putContents($file, $data);
	}
	
	/**
	 * 获取远程文件
	 */
	public function getUrlContents($key = 0, $_1 = null)
	{
		$file = $this->getProp($key, 'urls', $_1);
		return $str = Filesystem::getContents($file);
	}
	
	/**
	 * 获取本地文件
	 */
	public function getPathContents($key = 0, $_1 = null)
	{
		$file = $this->getProp($key, 'paths', $_1);
		return $str = Filesystem::getContents($file);
	}
	
	public function getSimpleXMLElement($data)
	{
		$rss = null;
		try {
			$rss = new \SimpleXMLElement($data);
			# $sx = new SimpleXML($data);
			# $rss = $sx->element;
		} catch (\Exception $e) {
			$rss = [$e->getMessage()];
		}
		return $rss;
	}
}
