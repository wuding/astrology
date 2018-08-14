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
	}
	
	public function __call($name, $arguments)
	{
		$tongji = $this->tongji;		
		$m3u8 = new \DbTable\HlsM3u8;		
		$title = '在线M3U8播放器';
		$url = '';
		$hide = 0;
		$where = null;
		$like = '';

		$row = $m3u8->findByName($name);
		if ($row) {
			$url = $row->url;
			$title = $row->title;
			$hide = 1;
			if ($row->playlist) {
				$where = ['playlist' => $row->playlist];
			}
		}
		$arr = $m3u8->fetchAll($where, 'title,name');

		include '../app/_Module/View/Index/play.php';
		exit;
	}
}
