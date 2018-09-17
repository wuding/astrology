<?php
namespace Controller;

use Astrology\Route;


class Index extends _Controller
{
	
	
	
	public function __construct()
	{
		parent::__construct();
		
		
	}
	
	
	
	public function index()
	{
		// 获取 uuid
		$item = [
			'code' => '200',
			'uuid' => $this->uuid,
		];
		if (!$this->uuid) {
			$item = $this->get_uuid();
		}
		
		// 检测 uuid
		if ('200' !== $item['code'] || !($uuid = $item['uuid'])) {
			echo file_get_contents('tmp/uuid_data.txt') . PHP_EOL;
			print_r($item);
			print_r([__LINE__, __METHOD__]);
			exit;
		}
		
		// 设置 uuid
		$_SESSION['uuid'] = $uuid;
		$url = "https://login.weixin.qq.com/qrcode/$uuid";
		echo "<img src='$url'>";			
		echo '<a href="/wechat/qrcode">qrcode</a>';
		# print_r(get_defined_vars());
	}
	
	
}
