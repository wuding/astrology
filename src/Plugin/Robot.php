<?php

namespace Plugin;

use Astrology\Extension\Filesystem;
use Astrology\Extension\SimpleXML;
use DbTable\VideoCollect;

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
		/*$all = $VideoCollect::query("SELECT * FROM `video_collect` LIMIT 50");
		foreach ($all as $key => $value) {
			print_r([$key, $value]);
		}*/
		# print_r(get_defined_constants());
		# exit;
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
		# print_r($arr);
		# exit;
		return ['result' => $arr];
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
