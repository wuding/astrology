<?php

namespace Astrology\Extension;

class Filesystem
{
	public function __construct()
	{
	}
	
	/**
	 * 检测是否是目录并自动创建
	 */
	public static function isDir($filename = null)
	{
		if (!is_dir($filename)) {
			return self::makeDir($filename);
		}
		return true;
	}
	
	/**
	 * 递归的创建目录
	 */
	public static function makeDir($pathname = null, $mode = 0777, $recursive = true)
	{
		return mkdir($pathname, $mode, $recursive);
	}
	
	/**
	 * 将字符串写入文件
	 */
	public static function putContents($filename = null, $data = null)
	{
		$dir = self::isDir(dirname($filename));
		return file_put_contents($filename, $data);
	}
	
	/**
	 * 将文件读入字符串
	 */
	public static function getContents($filename = null)
	{
		return file_get_contents($filename);
	}
}
