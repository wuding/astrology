<?php
namespace Astrology\Extension;

class Mbstring
{
	public $str = null;
	public $string = null;
	public $from_encoding = null;
	
	public function __construct($str = null, $from_encoding = null, $to_encoding = null)
	{
		if ($str) {
			$this->init($str, $from_encoding, $to_encoding);
		}
	}
	
	public function init($str, $from_encoding = null, $to_encoding = 'utf-8')
	{
		// 有些时候，参数默认值并不会有效，所以还是重新检测定义
		$to_encoding = $to_encoding ? : 'utf-8';
		$this->string = $str;
		if ($from_encoding) {
			$this->from_encoding = $from_encoding;
		} else {
			$from_encoding = $this->from_encoding;
		}
		return $this->str = mb_convert_encoding($str, $to_encoding, $from_encoding);
	}
	
	/**
	 * 
	 *
	 */
	public function preg_replace($pattern, $replace, $str = null)
	{
		if ($str) {
			$this->init($str);
		}
		
		$str = $this->str;
		return preg_replace($pattern, $replace, $str, 1);
	}
}
