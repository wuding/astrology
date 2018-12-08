<?php
/**
 * PECL - PHP Extension Community Library
 *
 * PHP 扩展社区库
 * https://pecl.php.net
 */

namespace Plugin\Robot;

class Pecl extends \Plugin\Robot
{
	/**
	 * 自定义初始化
	 *
	 */
	public function _init()
	{
		$this->cache_dir = $cache_dir = 'K:\www\cache\https\windows.php.net';
	}
	
	/**
	 * 下载列表
	 * 下载索引文件
	 */
	public function downloadList()
	{
		$path = '/downloads/pecl/snaps/';
		# https://windows.php.net/downloads/pecl/releases/
	}
	
	/**
	 * 解析列表
	 * 加入数据库
	 */
	public function parseList()
	{
		
	}
	
	/**
	 * 优化列表
	 * 从数据库下载下级目录索引
	 */
	public function optimizeList()
	{
		
	}
}
