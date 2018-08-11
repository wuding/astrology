<?php
namespace Controller;

use Astrology\Route;

class Index extends _Controller
{
	public function __construct()
	{
		parent::__construct();
	}
	
	public function __call($name, $arguments)
	{
		return [$name, $arguments, __FILE__, __LINE__];
	}
	
	public function index()
	{
		$tongji = $this->tongji;
		$arr = $this->playlist;
		$title = '在线M3U8播放器';
		$hide = 0;
		$query = isset($_GET['q']) ? trim($_GET['q']) : '';
		$url = $query;
		
		include '../app/_Module/View/Index/play.php';
		exit;
	}
}
