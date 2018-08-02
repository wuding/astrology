<?php

namespace Controller;

use Astrology\Route;

class _Controller extends \Astrology\Controller
{
	public $tongji = null;
	
	public function __construct()
	{
		# header("Access-Control-Allow-Origin: *");
		$stat = 0;
		if (isset($_GET['stat'])) {
			$stat = $_GET['stat'];
			setcookie('stat', $stat, time()+60*60*24*30, '/');
		} elseif (isset($_COOKIE['stat'])) {
			$stat = $_COOKIE['stat'];
		}
		$this->tongji = $stat;
		$this->page = $this->_get('page', 1, FILTER_VALIDATE_INT);
		$this->limit = $this->_get('limit', 1, FILTER_VALIDATE_INT);
	}
	/*
	public function _NotFound()
	{
		print_r([__METHOD__, __FILE__, __LINE__]);
	}
	
	public function _Action()
	{
		print_r([__METHOD__, __FILE__, __LINE__]);
	}
	*/
	public function __call($name, $arguments)
	{
		$tongji = $this->tongji;
		$arr = [
			'jlls3' => ['https://yong.yongjiu6.com/20180730/a0EpKQmn/index.m3u8', '精灵旅社3：疯狂假期'],
			'drjzsdtw' => ['https://v8.yongjiu8.com/20180731/rkOe3jjn/index.m3u8', '狄仁杰之四大天王'],
			'xhssf' => ['https://v8.yongjiu8.com/20180730/d3XWhZrA/index.m3u8', '西虹市首富'],
		];
		$title = '在线M3U8播放器';
		$url = '';
		$hide = 0;
		if (isset($arr[$name])) {
			$mov = $arr[$name];
			$url = $mov[0];
			$title = $mov[1];
			$hide = 1;
		}
		
		include '../app/_Module/View/Index/play.php';
		exit;
		return [$name, $arguments, __FILE__, __LINE__];
	}
}
