<?php
namespace Controller;

use Astrology\Route;
use Astrology\Extension\PhpCurl;
use Plugin\Robot\Alimama;

class _Controller extends \Astrology\Controller
{
	public $_enable_view = '';
	public $status = 0;
	
	public function __construct()
	{
		parent::__construct();
		$this->uuid = $this->_sess('uuid', '');
		$this->status = $this->_sess('redirect_uri', 0);
		$this->cookie = $this->_sess('weixin_cookie', '');
		$this->host = $this->_sess('host', '');
		$this->synckey = $this->_sess('synckey', '');
		$this->time = time();
		$this->rand = mt_rand(100000000, 999999999);
		$this->header = ['Content-Type: application/json; charset=UTF-8', "Referer: https://$this->host/", "Cookie: $this->cookie"];
		$this->header_get = ["Referer: https://$this->host/", "Cookie: $this->cookie"];
		$this->username = [];
	}
	
	public function __call($name, $arguments)
	{
		return [$name, $arguments, __FILE__, __LINE__];
	}
	
	public function _sess($key, $default = null)
	{
		return $val = (isset($_SESSION[$key]) && $_SESSION[$key]) ? $_SESSION[$key] : $default;
	}
	
	public function _json_output($data = [], $msg = '', $code = 0)
	{
		$value = [
			'code' => $code,
			'msg' => $msg,
			'data' => $data,
		];
		
		if (isset($_GET['type']) && 'json' == $_GET['type']) {
			echo $value = json_encode($value);exit;
		} else {
			print_r($value);
		}
	}
	
	public function test()
	{
		print_r($_SESSION);
	}
	
	public function restart()
	{
		# $_SESSION = [];
		unset($_SESSION['uuid'], $_SESSION['redirect_uri'], $_SESSION['weixin_cookie'], $_SESSION['host'], $_SESSION['synckey']);
		echo '<a href="/wechat">uuid</a>';
		
	}
	
	public function qrcode()
	{
		// 获取
		$url = "https://login.wx.qq.com/cgi-bin/mmwebwx-bin/login?loginicon=true&uuid=$this->uuid&tip=0&r=$this->rand&_=" . $this->time;
		$curl = new PhpCurl($url);			
		$data = $curl->download();
		file_put_contents('tmp/qrcode_data.txt', $data);
		
		// 检测
		$item = $this->parse_code($data);
		if ('200' != $item['code'] || !$item['redirect_uri']) {
			echo $data . PHP_EOL;
			print_r($item);
			print_r([__LINE__, __METHOD__]);
			exit;
		}
		
		// 设置
		$_SESSION['redirect_uri'] = $item['redirect_uri'];
		header('Location: /wechat/cookie');
		echo '<a href="/wechat/cookie">cookie</a>';
		# print_r(get_defined_vars());
	}
	
	public function cookie()
	{
		$redirect_url = $this->status;	
		$URL = parse_url($redirect_url);
		$_SESSION['host'] = $URL['host'];
		# print_r(get_defined_vars());exit;
		
		// 获取
		$curl = new PhpCurl($redirect_url);			
		$data = $curl->download(null, 1);
		$xml = new \SimpleXMLElement($data[1]);		
		$item = $this->parse_header($data[0]);
		file_put_contents('tmp/cookie_data.txt', serialize($data));
		
		//检测
		$item['cookie'] = $this->parse_cookie($item['set_cookie']);
		if ('/' == $item['location'] && $item['cookie']) {
			$_SESSION['weixin_cookie'] = $item['cookie'];
			
		} else {
			print_r($item);
			print_r([__LINE__, __METHOD__]);
			exit;
		}
		header('Location: /wechat/init');
		echo '<a href="/wechat/init">init</a>';
		# print_r(get_defined_vars());
    }
	
	public function init()
	{
		# print_r($_SESSION);exit;
		$first = mt_rand(1000000, 1999999);
		$second = mt_rand(10000000, 99999999);
		$rand = $first . $second;
		
		# $data = unserialize($this->_sess('cookie_data') ? : file_get_contents('tmp/cookie_data.txt'));
		$data = unserialize(file_get_contents('tmp/cookie_data.txt'));
		$xml = new \SimpleXMLElement($data[1]);
		
		$arr = [
			'BaseRequest' => [
				'Uin' => (int) $xml->wxuin,
				'Sid' => "$xml->wxsid",
				'Skey' => "$xml->skey", # 
				'DeviceID' => 'e' . $rand,
			],
		];
		$base = json_encode($arr);
		$arr['BaseRequest']['Skey'] = '';
		$json = json_encode($arr);
		
		$url = "https://$this->host/cgi-bin/mmwebwx-bin/webwxinit?r=$this->rand";
		# $url .= "&lang=zh_CN&pass_ticket=" . urlencode($xml->pass_ticket);
		$curl = new PhpCurl($url);
		$data = $curl->simulate($json, $this->header);
		
		file_put_contents('tmp/init_data.txt', $data);
		file_put_contents('tmp/base_request.txt', $base);
		# $data = file_get_contents('tmp/init_data.txt');
		$obj = json_decode($data);
		if (0 != $obj->BaseResponse->Ret) {
			print_r($obj);
			print_r([__LINE__, __METHOD__]);
			exit;
		}
		
		$key = $obj->SyncKey->List;
		$this->update_synckey($key);
		
		header('Location: /wechat/notify');
		echo '<a href="/wechat/notify">notify</a>';
		# print_r(get_defined_vars());
	}
	
	public function notify()
	{
		$data = unserialize(file_get_contents('tmp/cookie_data.txt'));
		$xml = new \SimpleXMLElement($data[1]);
		
		$data = file_get_contents('tmp/init_data.txt');
		$init = json_decode($data);
		
		$base = file_get_contents('tmp/base_request.txt');
		$obj = json_decode($base);
		# $obj->Skey = $xml->skey;
		$arr = (array) $obj;
		$info = [
			'ClientMsgId' => $this->time,
			'Code' => 3,
			'FromUserName' => $this->_get('from', $init->User->UserName),
			'ToUserName' => $this->_get('to', $init->User->UserName),
		];
		$arr += $info;
		$json = json_encode($arr);
		
		$url = "https://$this->host/cgi-bin/mmwebwx-bin/webwxstatusnotify";
		$curl = new PhpCurl($url);
		$data = $curl->simulate($json, $this->header);
		
		file_put_contents('tmp/notify_data.txt', $data);
		header('Location: /wechat/contact');
		echo '<a href="/wechat/contact">contact</a>';
		# print_r(get_defined_vars());
	}
	
	public function contact()
	{
		
		$data = unserialize(file_get_contents('tmp/cookie_data.txt'));
		$xml = new \SimpleXMLElement($data[1]);
		
		$url = "https://$this->host/cgi-bin/mmwebwx-bin/webwxgetcontact?r=$this->time&seq=0&skey=$xml->skey";
		$curl = new PhpCurl($url);
		$data = $curl->simulate(null, $this->header_get);
		
		file_put_contents('tmp/contact_data.txt', $data);
		/*
		$data = file_get_contents('tmp/contact_data.txt');
		*/
		$this->user_name($data);
		header('Location: /wechat/check');
		echo '<a href="/wechat/check">check</a>';
		# print_r(get_defined_vars());
	}
	
	public function check()
	{
		$base = file_get_contents('tmp/base_request.txt');
		$obj = json_decode($base);
		
		$data = unserialize(file_get_contents('tmp/cookie_data.txt'));
		$xml = new \SimpleXMLElement($data[1]);
		
		$query_data = [
			'r' => $this->time,
			'skey' => (string) $xml->skey,
			'sid' => (string) $xml->wxsid,
			'uin' => (string) $xml->wxuin,
			'deviceid' => $obj->BaseRequest->DeviceID,
			'synckey' => $this->synckey,
			'_' => $this->time,
		];		
		$query_string = http_build_query($query_data);
		
		$url = "https://webpush.$this->host/cgi-bin/mmwebwx-bin/synccheck?$query_string";
		$curl = new PhpCurl($url);
		$data = $curl->simulate(null, $this->header_get);
		$item = $this->parse_code($data);
		file_put_contents('tmp/check_data.txt', $data);
		
		$synccheck = $item['synccheck'];
		$synccheck = preg_replace('/({|,)([a-z]+):/i', '$1"$2":', $synccheck);
		$obj = json_decode($synccheck); # print_r($obj);
		$code = 1;
		if ($obj->retcode) {
			/*
			print_r([__LINE__, __METHOD__]);
			exit;
			*/
			$code = 1;
		}
		
		# print_r($item);
		$this->_json_output($obj, json_encode($obj), $code);
		echo '<a href="/wechat/batch">batch</a>';
		# print_r(get_defined_vars());
		
	}
	
	public function batch()
	{
		$data = file_get_contents('tmp/init_data.txt');
		$init = json_decode($data);
		$set = $init->ChatSet;
		$sets = explode(',', $set);
		$list = [];
		foreach ($sets as $row) {
			if (preg_match('/^@@/', $row)) {
				$list[] = [
					'UserName' => $row,
					'ChatRoomId' => '',
				];
			}
		}
		
		
		$data = unserialize(file_get_contents('tmp/cookie_data.txt'));
		$xml = new \SimpleXMLElement($data[1]);
		
		$base = file_get_contents('tmp/base_request.txt');
		$obj = json_decode($base); # print_r($obj);
		# $obj->BaseRequest->Skey = $xml->skey;print_r($obj);
		$arr = (array) $obj;
		$info = [
			'Count' => count($list),
			'List' => $list,
		];
		$arr += $info;
		$json = json_encode($arr);
		# print_r($arr);print_r($json);exit;
		
		$url = "https://$this->host/cgi-bin/mmwebwx-bin/webwxbatchgetcontact?type=ex&r=$this->time";		
		$curl = new PhpCurl($url);
		$data = $curl->simulate($json, $this->header);
		file_put_contents('tmp/batch_data.txt', $data);
		echo '<a href="/wechat/sync">sync</a>';
		# print_r(get_defined_vars());
	}
	
	public function sync()
	{
		
		$data = file_get_contents('tmp/init_data.txt');
		$init = json_decode($data);
		
		$data = unserialize(file_get_contents('tmp/cookie_data.txt'));
		$xml = new \SimpleXMLElement($data[1]);
		
		$base = file_get_contents('tmp/base_request.txt');
		$obj = json_decode($base);
		# $obj->SyncKey = $init->SyncKey;print_r($obj);
		$obj->SyncKey = $this->convert_synckey(); # print_r($obj);exit;
		$json = json_encode($obj);

		/**/
		$url = "https://$this->host/cgi-bin/mmwebwx-bin/webwxsync?sid=$xml->wxsid&skey=$xml->skey&pass_ticket=$xml->pass_ticket";
		$curl = new PhpCurl($url);
		$data = $curl->simulate($json, $this->header, 1);
		# print_r($data);exit;
		
		/*
		$data = unserialize(file_get_contents('tmp/sync_data.txt'));
		
		
		$data = [];
		$data[0] = file_get_contents('tmp/sync_data.html');
		$data[1] = file_get_contents('tmp/sync_data.json');
		*/
		
		file_put_contents('tmp/sync_data.html', $data[0]);
		file_put_contents('tmp/sync_data.json', $data[1]);
		
		
		$json = $data[1];
		$obj = json_decode($json);
		
		$dat = [];
		$code = 1;
		if (0 == $obj->BaseResponse->Ret) {
			$item = $this->parse_header($data[0]);
			# file_put_contents('tmp/sync_data.txt', serialize($data));
			
			
			if (isset($item['set_cookie'])) {
				$cookie = $this->parse_cookie($item['set_cookie']);
				$_SESSION['weixin_cookie'] = $this->merge_cookie($cookie);
			}
			
			
			if ($obj->SyncKey->Count) {
				$key = $obj->SyncKey->List;
				$this->update_synckey($key);
			}
			
			$dat = [$obj->AddMsgCount];
			if ($obj->AddMsgCount) {
				$dat = $this->msg_list($obj->AddMsgList, $init->User->UserName); # 
			}
			# print_r($obj);exit;
		}
		
		$this->_json_output($dat, json_encode($dat), $code);
		echo '<a href="/wechat/check">check</a>';
	}
	
	
	
	public function msg($to = null, $content = null)
	{
		# $first = mt_rand(1000000, 9999999);
		$id = $this->time; #  . $first;
		$data = file_get_contents('tmp/init_data.txt');
		$init = json_decode($data);
		
		$to = $to ? : $this->_get('to', $init->User->UserName);
		$content = $content ? : $this->_get('content', '');
		
		$base = file_get_contents('tmp/base_request.txt');
		$obj = json_decode($base);
		$arr = (array) $obj;
		$info = [
			'Msg' => [
				'ClientMsgId' => "$id",
				'Content' => '<%1>',
				'FromUserName' => $init->User->UserName,
				'LocalID' => "$id",
				'ToUserName' => $to,
				'Type' => 1,
			],
			'Scene' => 0,
		];
		$arr += $info;
		$json = json_encode($arr);
		$json = str_replace('<%1>', addcslashes($content, '"') . time(), $json);
		# print_r(get_defined_vars());exit;
		
		$url = "https://$this->host/cgi-bin/mmwebwx-bin/webwxsendmsg";
		$curl = new PhpCurl($url);
		$data = $curl->simulate($json, $this->header);
		file_put_contents("tmp/msg_data_$to.txt", $data);
	}
	
	public function msg_list($list, $self = null)
	{
		$arr = [];
		foreach ($list as $row) {
			// 个人用户
			if (!preg_match('/^@@/', $row->FromUserName)) {
				// 不是发给自己
				if ($row->FromUserName != $row->ToUserName && $self != $row->FromUserName) { # print_r([$self, $row]);
					// 可能更新了
					if ($self != $row->ToUserName) {
						file_put_contents("tmp/msg_list.txt", $self . '_' . $row->FromUserName . '_' . $row->ToUserName);
					}
					if ($row->Content) {
						# $user = '@08894d343fad2ce140a395ec6c6f8079';
						$user = $row->FromUserName;
						$coupon = '';
						if (preg_match('/http(|s):\/\/m.tb.cn\/([a-z0-9\.]+)/i', $row->Content, $matches)) {
							#print_r($matches);
							$url = 'https://m.tb.cn/' . $matches[2];
							$alimama = new Alimama();
							$info = $alimama->downloadCoupon($url);
							
							if (200 == $info['code']) {
								$coupon = $info;
							} else {
								$coupon = 200;
							}
						}
						
						$arr[] = [
							'msg_id' => $row->MsgId,
							'msg_type' => $row->MsgType,
							'user_name' => $user,
							'nick_name' => $this->nick_name($user),
							'content' => $row->Content,
							'create_time' => $row->CreateTime,
							'coupon' => $coupon,
						];
					}
				}
			}
		}
		
		foreach ($arr as $row) { # print_r($row);exit;
			$obj = $row['coupon'];
			if ($obj) {
				if (!is_numeric($obj)) {
					extract($row['coupon']);
					$msg = '[机器人助手]自动回复：稍后小主将回复您，谢谢！';
					$msg = sprintf('返利%s%% 约￥%s 〖优惠券￥%s 券后价￥%s〗 【%s】 淘口令 %s 领券链接 %s', $rate, $fee, $amount, $price, $title, $command, $url);
					$this->msg($row['user_name'], $msg . '?unid=' . $row['msg_id']); # 
				} else {
					$msg = '没有找到返利信息！更多优惠券 https://www.cpn.red/';
					$this->msg($row['user_name'], $msg);
				}
			}
		}
		# print_r($arr);
		return $arr;
	}
	
	public function nick_name($user)
	{
		if (array_key_exists($user, $this->username)) {
			return $this->username[$user];
		}
		
		$data = file_get_contents('tmp/username.txt');
		$obj = json_decode($data); # print_r($obj);exit;
		#$list = $obj->MemberList;
		$arr = [];
		foreach ($obj as $key => $value) {
			# $arr[$key] = $value;
			if ($user == $key) {
				return $this->username[$user] = $value;
			}
		}
		return '';
	}
	
	public function user_name($data)
	{
		$obj = json_decode($data);
		$list = $obj->MemberList;
		$arr = [];
		foreach ($list as $row) {
			$arr[$row->UserName] = $row->NickName;
		}
		$json = json_encode($arr);
		file_put_contents('tmp/username.txt', $json);

	}
	
	public function convert_synckey($key = null)
	{
		$key = $key ? : $this->synckey;
		$arr = explode('|', $key);
		
		$list = [];
		foreach ($arr as $row) {
			if ($row) {
				$itm = explode('_', $row);
				$list[] = [
					'Key' => $itm[0],
					'Val' => $itm[1],
				];
			}
		}
		
		$info = [
			'Count' => count($list),
			'List' => $list,
		];
		return $info;
	}
	
	public function update_synckey($key)
	{
		$ar = [];
		foreach ($key as $row) {
			$ar[] = $row->Key . '_' . $row->Val;
		}
		$_SESSION['synckey'] = implode('|', $ar);
	}
	
	public function get_uuid()
	{
		// 获取 uuid 代码
		$url = 'https://login.wx.qq.com/jslogin?appid=wx782c26e4c19acffb&redirect_uri=https%3A%2F%2Fwx.qq.com%2Fcgi-bin%2Fmmwebwx-bin%2Fwebwxnewloginpage&fun=new&lang=zh_CN&_=' . $this->time;
		$curl = new PhpCurl($url);
		$data = $curl->download();		
		/*$data = 'window.QRLogin.code = 200; window.QRLogin.uuid = "AZKpTuJ27Q==";';*/
		# $_SESSION['uuid_data'] = $data;
		file_put_contents('tmp/uuid_data.txt', $data);
		return $item = $this->parse_code($data);
		
	}
	
	public function parse_code($data)
	{
		$str = trim($data);
		$str = trim($str, ';');
		$arr = preg_split('/;\s+/', $str); # print_r($arr);
		$item = [];
		foreach ($arr as $row) {
			$ar = preg_split('/=/', $row, 2); # print_r($ar);
			$exp = explode('.', trim($ar[0]));
			$count = count($exp);
			$key = $exp[$count - 1];
			$val = trim($ar[1]);
			$val = trim($val, '"');
			$item[$key] = $val;
		}
		# print_r($item);
		return $item;
	}
	
	public function parse_header($subject)
	{
		$headers = preg_split('/\r\n/', $subject);
		# print_r($headers);
		$item = [];
		foreach ($headers as $header) {
			$arr = preg_split('/:\s+/', $header, 2); # print_r($arr);
			$count = count($arr);
			$key = $arr[0];
			if (2 > $count) {
				if (preg_match('/([a-z]+)\/([0-9\.]+)\s+(\d+)\s+(.*)/i', $key, $matches)) {
					# print_r($matches);
					$key = $matches[1];
					$item[$key] = $matches;
					continue;
				} else {
					print_r($arr);
				}
			}
			$val = $arr[1];
			
			$key = preg_replace('/\-/', '_', $key);
			$key = strtolower($key);
			if (isset($item[$key])) {
				if (!is_array($item[$key])) {
					$item[$key] = [$item[$key]];
				}
				$item[$key][] = $val;
			} else {
				$item[$key] = $val;
			}
		}
		# print_r($item);
		return $item;
	}
	
	public function parse_cookie($data)
	{
		$cookies = [];
		foreach ($data as $row) {
			$arr = preg_split('/;\s+/', $row); # print_r($arr);
			$count = count($arr);
			$j = 0;
			for ($i = 1; $i < $count; $i++) {
				
				$r = $arr[$i];
				$ar = preg_split('/=/', $r, 2); # print_r($ar);
				$key = strtolower($ar[0]);
				$val = isset($ar[1]) ? $ar[1] : null;
				if ('expires' == $key) {
					$time = strtotime($val);
					if ($this->time > $time) {
						$j = 1;
						/*
						$date = date('Y-m-d H:i:s', $time);
						echo $date . PHP_EOL;
						*/
						
					}
				}
				
				
			}
			
			if (!$j) {
				$cookies[] = $arr[0];
			}
		}
		# print_r($cookies);
		return $str = implode('; ', $cookies);
	}
	
	public function merge_cookie($cookie)
	{
		$arr = preg_split('/;\s+/', $cookie); # print_r($arr);
		$ar = preg_split('/;\s+/', $_SESSION['weixin_cookie']); # print_r($ar);
		
		$cookies = [];		
		foreach ($ar as $row) {
			$itm = preg_split('/=/', $row, 2); # print_r($itm);
			$cookies[$itm[0]] = $itm[1];
		}
		foreach ($arr as $row) {
			$itm = preg_split('/=/', $row, 2); # print_r($itm);
			$cookies[$itm[0]] = $itm[1];
		}
		
		$ck = [];
		foreach ($cookies as $key => $value) {
			$ck[] = "$key=$value";
		}
		return $str = implode('; ', $ck);
		# print_r($cookies);
	}
}
