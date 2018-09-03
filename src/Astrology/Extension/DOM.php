<?php
namespace Astrology\Extension;

class DOM
{
	public $str = null;
	public $doc = null;
	public $html = null;
	
	public function __construct($str = null, $charset = null)
	{
		if ($str) {
			$this->init($str, $charset);
		}
	}
	
	public function init($str, $charset = null)
	{
		$this->str = $str;
		if ($charset) {
			$str = '<!doctype html>
<html>
<head><meta charset="utf-8"></head>
<body>' . $str . '<body>
</html>';
			$this->html = $str;
		}

		$doc = new \DOMDocument();
		@$doc->loadHTML($str);
		return $this->doc = $doc;
	}
}
