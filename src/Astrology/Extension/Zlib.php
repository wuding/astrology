<?php
namespace Astrology\Extension;

class Zlib
{
	/**
	 * 解压一个gzip压缩的单文件
	 *
	 */
	public static function uncompress($gz_file, $filename)
	{
		/*
		$handle = gzopen($path, 'r');
		while (!gzeof($handle)) {
		   $buffer = gzgets($handle);
		   $str .= $buffer;
		}
		gzclose($handle);
		*/
		$data = file_get_contents($gz_file);
		$str = gzdecode($data);
		return file_put_contents($filename, $str);
	}
}
