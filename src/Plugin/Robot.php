<?php

namespace Plugin;

use Astrology\Extension\Filesystem;

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
}
