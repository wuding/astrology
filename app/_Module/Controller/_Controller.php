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
			$row = $coupon->find($where, 'promotion,`group`,taobaoke,name,pic', 'id DESC');
			if ($row) {
				$url = $row->promotion;
				if ($row->group) {
					$url = $row->taobaoke;
				}
				$this->_output($url, $row->name, $row->pic, '.' . $code);
			}
		}
		
		$this->_NotFound([$row, __METHOD__, __FILE__, __LINE__]);
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
				$this->_output($url, $row->title, null, '!' . $code);
			}
		}
		
		$this->_NotFound([$row, __METHOD__, __FILE__, __LINE__]);
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
			$row = $coupon->find($where, 'url,`group`,taobaoke,name,pic', 'id DESC');
			if ($row) {
				$url = $row->taobaoke;
				if ($row->group) {
					$url = $row->url;
				}
				$this->_output($url, $row->name, $row->pic, $code);
			}
		}
		
		$this->_NotFound([$row, __METHOD__, __FILE__, __LINE__]);
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
					$command_url = 'http://coupon.ren/\$' . $row->command . '\$';
					$command_url = "<a href=\"$command_url\">$command_url</a>";# 
					if (!$row->description) {
						$row->description = '【%title】，復·制这段描述 %command_url 后咑閞👉手机淘宝👈或者用浏览器咑閞查看';
					}
					$row->description = preg_replace("/%title/", $row->title, $row->description);
					$row->description = preg_replace("/%command_url/", $command_url, $row->description);
					$this->_output($row->url, [$row->title, $row->description], $row->pic, '$' . $code . '$');
				}
			} else {
				$coupon->insert(['command' => $code, 'symbol' => $type, 'created' => time()]);
			}
		}
		# print_r([$GLOBALS['PATH'], $matches, $row]);exit;
		$this->_NotFound([$row, __METHOD__, __FILE__, __LINE__]);
	}
	
	public function _output($url, $name = '', $pic = '', $code = '')
	{
		$redirect = isset($_GET['debug']);
		$html = '';
		$title = '凑婆娘优惠券_短网址服务 缩址工具 更方便!';
		$request_uri = htmlspecialchars($_SERVER['REQUEST_URI']);
		$source_url = 'http://' . $_SERVER['HTTP_HOST'] . $request_uri;
		$device = '未知APP';
		$client = 0;
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
			
			$html = <<<HEREDOC
			<h4 style="background:#f00; color:#fff; padding: 10px; margin: 0; text-align: center">
如被重新排版, 请点击上面的<a href="http://{$_SERVER['HTTP_HOST']}$request_uri">访问原网页</a>!!!
</h4>
			<div style="background:#000; color:#fff; padding: 10px">
		点击右上角... 选择在 Safari 浏览器中打开, 您正在使用的 {$device} 无法正常查看!
</div>
HEREDOC;
		} else {
			$html .= "<h3 style=\"text-align: center; background:#000; color:#fff;  padding: 10px; margin: 0\">页面跳转中……<br/>请稍候</h3>";
			if (!$redirect) {
				$html .= "<script>window.scrollTo(0,0); setTimeout(\"location.href='$url'\", 1000);</script><iframe src=\"$url\" style=\"width: 1px; height: 1px; border: 0; margin: 0; padding: 0; float: left;\"></iframe>";# 
			}
		}
		$html .= "<blockquote style=\"text-align: center\"><a href=\"$url\">按住这里复制链接地址</a></blockquote>";
		
		if ($name) {
			if (is_array($name)) {
				$title = htmlspecialchars($name[0]);
				$name = $name[1] ? : $title;
			} else {
				$title = $name = htmlspecialchars($name);
			}
			$title .= '_凑婆娘优惠券';
			# $name = "<div style=\"text-align: center\">$name</div>";
			
		}
		if ($pic) {
			$pic = "<div><img src=\"$pic\" style=\"width: 100%\"></div>";
		}
		
		$tongji = $this->tongji;
		include '../app/_Module/View/_Controller/output.html';
		exit;
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
		exit;
	}
}
