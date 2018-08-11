<?php
namespace Controller;

use Astrology\Route;

class _Controller extends \Astrology\Controller
{
	public $tongji = null;
	public $playlist = [];
	
	public function __construct()
	{
		$stat = 0;
		if (isset($_GET['stat'])) {
			$stat = $_GET['stat'];
			setcookie('stat', $stat, time()+60*60*24*30, '/');
		} elseif (isset($_COOKIE['stat'])) {
			$stat = $_COOKIE['stat'];
		}
		$this->tongji = $stat;
		
		$arr = [
			'jlls3' => ['https://yong.yongjiu6.com/20180730/a0EpKQmn/index.m3u8', '精灵旅社3：疯狂假期'],
			'drjzsdtw' => ['https://v8.yongjiu8.com/20180731/rkOe3jjn/index.m3u8', '狄仁杰之四大天王'],
			'xhssf' => ['https://v8.yongjiu8.com/20180730/d3XWhZrA/index.m3u8', '西虹市首富'],
			'fczlm3' => ['https://v8.yongjiu8.com/20180801/k5JHNg2q/index.m3u8', '复仇者联盟3：无限战争'],
			'pottygirl253' => ['https://cdn.zypll.com/20180801/StYVMohv/index.m3u8', 'pottygirl写真253'],
			'xmccjrs1' => ['https://boba.52kuyun.com/20180803/U7DtIkd9/index.m3u8', '香蜜沉沉烬如霜 第1集'],
			'xmccjrs2' => ['https://boba.52kuyun.com/20180803/5r3NK9Do/index.m3u8', '香蜜沉沉烬如霜 第2集'],
			'xmccjrs3' => ['https://v-xunlei.com/20180803/6559_3b60f76d/index.m3u8', '香蜜沉沉烬如霜 第3集'],
			'xmccjrs4' => ['https://v-xunlei.com/20180803/6562_abf9974d/index.m3u8', '香蜜沉沉烬如霜 第4集'],
			'xmccjrs5' => ['http://v-xunlei.com/20180804/6613_a909360f/index.m3u8', '香蜜沉沉烬如霜 第5集'],
			'xmccjrs6' => ['http://cdn.zypbo.com/20180806/NaLvKG96/index.m3u8', '香蜜沉沉烬如霜 第6集'],
			'xmccjrs7' => ['https://cdn.zypbo.com/20180806/HJu8Uyon/index.m3u8', '香蜜沉沉烬如霜 第7集'],
			'xmccjrs8' => ['http://yong.yongjiu6.com/20180806/Z6aAxnXL/index.m3u8', '香蜜沉沉烬如霜 第8集'],
			'xmccjrs9' => ['http://yong.yongjiu6.com/20180806/fkfhdmJU/index.m3u8', '香蜜沉沉烬如霜 第9集'],
			'xmccjrs10' => ['https://v-xunlei.com/20180807/6824_1b0a7cf7/index.m3u8', '香蜜沉沉烬如霜 第10集'],
			'xmccjrs11' => ['https://v-xunlei.com/20180807/6825_ec095e73/index.m3u8', '香蜜沉沉烬如霜 第11集'],
			'xmccjrs12' => ['https://v-xunlei.com/20180808/6903_17b6ece6/index.m3u8', '香蜜沉沉烬如霜 第12集'],
			'xmccjrs13' => ['https://v-xunlei.com/20180808/6904_633d279c/index.m3u8', '香蜜沉沉烬如霜 第13集'],
			'xmccjrs14' => ['https://v-xunlei.com/20180809/6948_ee5f7d34/index.m3u8', '香蜜沉沉烬如霜 第14集'],
			'xmccjrs15' => ['https://v-xunlei.com/20180809/6949_ccf084f4/index.m3u8', '香蜜沉沉烬如霜 第15集'],
		];
		
		/*
		$i = 0;
		$sort = [];
		foreach ($arr as $key => $value) {
			$sort[$i] = $key;
			$i++;
		}
		krsort($sort);
		
		$new = [];
		foreach ($sort as $val) {
			$new[$val] = $arr[$val];
		}
		*/
		$this->playlist = array_reverse($arr);
	}
	
	public function __call($name, $arguments)
	{
		$tongji = $this->tongji;
		$arr = $this->playlist;
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
	}
}
