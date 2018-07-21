<?php

namespace Controller;

class _Controller extends \Astrology\Controller
{
	public $tongji = null;
	
	public function __construct()
	{
		# parent::__construct();
		# print_r([__METHOD__, __FILE__, __LINE__]);# 
		# print_r($_SERVER['HTTP_USER_AGENT']);
		# exit;
		
		$stat = 0;
		if (isset($_GET['stat'])) {
			$stat = $_GET['stat'];
			setcookie('stat', $stat, time()+60*60*24*30, '/');
		} elseif (isset($_COOKIE['stat'])) {
			$stat = $_COOKIE['stat'];
		}
		$this->tongji = $stat;
		$this->redirect = isset($_GET['redirect']);
		$this->timeout = isset($_GET['timeout']) ? $_GET['timeout'] : null;
		$this->url_shortening = $GLOBALS['CONFIG']['view']['url_shortening'];
	}
	
	/*public function _Action()
	{
		print_r([__METHOD__, __FILE__, __LINE__]);
	}*/
	
	/**
	 * 领券
	 */
	public function promotion()
	{
		if (preg_match("/^\/\.+([a-z0-9]+)(.*)/", $GLOBALS['PATH'], $matches)) {			
			$code = $matches[1];			
			$item = base_convert($code, 36, 10);
			
			$where = "item = $item";
			$group = '=';
			$postfix = $matches[2];
			if ($postfix && preg_match("/^!+(.*)/", $postfix, $matche)) {
				$type = $matche[1];
				if ('ju' == $type) {
					$group = '>';
				}
			}
			$where .= " AND `group` $group 0";
			
			$coupon = new \DbTable\AlimamaCoupon;
			$row = $coupon->find($where, 'promotion,`group`,taobaoke,name,pic,qr,timeout', 'id DESC');
			if ($row) {
				$url = $row->promotion;
				if ($row->group) {
					$url = $row->taobaoke;
				}
				$this->_output($url, $row->name, $row->pic, '.' . $code, $row->qr, 'promotion', $row->timeout);
			}
		} else {
			$this->_NotFound([$row, __METHOD__, __FILE__, __LINE__]);
		}
	}
	
	/**
	 * 缩址
	 */
	public function shortening()
	{
		if (preg_match("/^\/!+([a-z0-9]+)/", $GLOBALS['PATH'], $matches)) {
			$code = $matches[1];
			$id = base_convert($code, 36, 10);
			
			$coupon = new \DbTable\CouponShortening;
			$row = $coupon->find("id = $id");
			if ($row) {
				$url = $row->url;
				$this->_output($url, $row->title, null, '!' . $code, $row->qr, 'shortening', $row->timeout);
			}
		} else {
			$this->_NotFound([$row, __METHOD__, __FILE__, __LINE__]);
		}
	}
	
	/**
	 * 详情
	 */
	public function item()
	{
		if (preg_match("/^\/+([0-9]+)(.*)/", $GLOBALS['PATH'], $matches)) {# print_r($matches);
			$code = $matches[1];
			
			$where = "item = $code";
			$group = '=';
			$postfix = $matches[2];
			if ($postfix && preg_match("/^!+(.*)/", $postfix, $matche)) {
				$type = $matche[1];
				if ('ju' == $type) {
					$group = '>';
				}
			}
			$where .= " AND `group` $group 0";
			
			$coupon = new \DbTable\AlimamaCoupon;
			$row = $coupon->find($where, 'url,`group`,taobaoke,name,pic,qr,timeout', 'id DESC');
			if ($row) {
				$url = $row->taobaoke;
				if ($row->group) {
					$url = $row->url;
				}
				$this->_output($url, $row->name, $row->pic, $code, $row->qr, 'item', $row->timeout);
			}
		} else {
			$this->_NotFound([$row, __METHOD__, __FILE__, __LINE__]);
		}
	}
	
	/**
	 * 淘口令
	 */
	public function command()
	{
		if (preg_match("/^\/(\\$|¥|€)([a-zA-Z0-9]+)(.*)/", $GLOBALS['PATH'], $matches)) {
			$type = $matches[1];
			$code = $matches[2];
			
			$where = "command = '$code'";
			# $where .= " AND `symbol` = '$type'";
			
			$coupon = new \DbTable\TaobaoCommand;
			$coupon->query("SET NAMES utf8mb4");# 
			$row = $coupon->find($where, '*', 'id DESC');
			if ($row) {
				if ($row->url) {
					$command_url = $this->url_shortening . '\$' . $row->command . '\$';
					$command_url = "<a href=\"$command_url\">$command_url</a>";# 
					if (!$row->description) {
						# $row->description = '【%title】，復·制这段描述 %command_url 后咑閞👉手机淘宝👈或者用浏览器咑閞查看';
					}
					$row->description = preg_replace("/%title/", $row->title, $row->description);
					# $row->description = preg_replace("/%command_url/", $command_url, $row->description);
					$this->_output($row->url, [$row->title, $row->description], [$row->pic, $row->img], '$' . $code . '$', $row->qr, 'command', $row->timeout);# 
				}
			} else {
				$coupon->insert(['command' => $code, 'symbol' => $type, 'created' => time()]);
			}
		} else {
			$this->_NotFound([$row, __METHOD__, __FILE__, __LINE__]);
		}
		# print_r([$GLOBALS['PATH'], $matches, $row]);exit;
	}
	
	public function _output($url, $name = '', $pic = '', $code = '', $qr = null, $type = null, $timeout = 5)
	{
		$timeout = is_null($this->timeout) ? $timeout : $this->timeout;
		if (0 > $timeout) {
			header("Location: $url");
			exit;
		}
		
		$redirect = isset($_GET['debug']);
		$html = $description = '';
		$title = '凑婆娘优惠券_短网址服务 缩址工具 更方便!';
		$request_uri = htmlspecialchars($_SERVER['REQUEST_URI']);
		$source_url = 'http://' . $_SERVER['HTTP_HOST'] . $request_uri;
		$device = '未知APP';
		$client = null;
		if (preg_match("/AliApp\((TB|AP)\/([0-9\.]+)\)/i", $_SERVER['HTTP_USER_AGENT'], $matches)) {
			# print_r($matches);exit;			
			$client = $matches[1];
			$version = $matches[2];
			/*
			header("Location: $url");
			exit;*/
		} elseif (preg_match("/(MicroMessenger|QQ)/i", $_SERVER['HTTP_USER_AGENT'], $matches)) {
			$client = $matches[1];
			$device = $matches[1];
		}
		
		$command_url = $this->url_shortening . $code;
		if ($name) {
			if (is_array($name)) {				
				$description = $name[1];
				$name = htmlspecialchars($name[0]);
				
				$description = preg_replace("/%command_url/", $command_url, $description);
			}
			$title = $name;
			$title .= '_凑婆娘优惠券';			
		}
		
		$tongji = $this->tongji;
		$second = $timeout;
		$open_url = preg_replace("/^http(|s):\/+/i", '', $command_url);
		$cmd_url = htmlspecialchars($command_url);
		$urlencode = urlencode($code);
		$url_encode = ($urlencode != $code) ? $this->url_shortening . $urlencode : '';
		
		# print_r(get_defined_vars()); exit;
		include '../app/_Module/View/_Controller/output.html';
		# exit;
	}
	
	/**
	 * 未找到动作
	 */
	public function _NotFound($arr = [])
	{
		$title = '凑婆娘优惠券_短网址服务 缩址工具 更方便!';
		$html = '';
		$html .= "<h3 style=\"text-align: center; background:#000; color:#fff;  padding: 10px; margin: 0\">404 页面未找到</h3>";
		$html .= "<script>window.scrollTo(0,0); setTimeout(\"location.href='https://www.coupons.name/'\", 3000);</script>";
		$html .= "<blockquote style=\"text-align: center\">即将转到 <a href=\"https://www.coupons.name\">www.coupons.name</a></blockquote>";
		$info = print_r($arr, true);
		
		$tongji = $this->tongji;
		include '../app/_Module/View/_Controller/notfound.php';
		# exit;
	}
}
