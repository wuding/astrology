<?php
/**
 * 淘宝联盟 - 阿里妈妈
 *
 * https://pub.alimama.com/
 */
namespace Plugin\Robot;

use DbTable\AlimamaChoiceExcel;
use DbTable\AlimamaChoiceList;
use DbTable\AlimamaProductCategory;
use Astrology\Extension\PhpCurl;

class Alimama extends \Plugin\Robot
{
	public $overwrite = true;
	
	#! public $site_id = null;
	public $api_host = 'http://lan.urlnk.com';
	public $csv_encoding = 'utf-8';
	
	/**
	 * 自定义初始化 
	 */
	public function _init()
	{
		$this->bill = $bill = isset($_GET['bill']) ? (int) $_GET['bill'] : 1;		
		$this->cache_dir = $cache_dir = CACHE_ROOT . '/http/www.alimama.com';
		$this->api_host = 'http://' . $_SERVER['HTTP_HOST'];
		$date = date('m-d');
		
		$this->paths = [
			$cache_dir . "/$bill/$date.csv",
            $cache_dir . "/$bill/$date.xls", //精选优质商品清单（内含优惠券）
			$cache_dir . "/$bill/$date.xls", //春节活动, 9.9大促预售爆款
			$cache_dir . "/$bill/$date.xls", //聚划算拼团单品（建议转换淘口令传播）
			$cache_dir . "/4/%1.json", //超级搜索 - 高佣活动
        ];
		
		$this->urls = [
			"",
			"https://pub.alimama.com/coupon/qq/export.json?adzoneId=126228652&siteId=35532130",
			'https://pub.alimama.com/operator/tollbox/excelDownload.json?excelId=MKT_HOT_EXCEL_LIST&adzoneId=126228652&siteId=35532130',
			"https://pub.alimama.com/operator/tollbox/excelDownload.json?excelId=JUPINTUAN_LIST&adzoneId=126228652&siteId=35532130",
			"https://pub.alimama.com/items/channel/qqhd.json?channel=qqhd&toPage=%1&sortType=13&dpyhq=1&perPageSize=50&shopTag=dpyhq&t=1531379311587&_tb_token_=&pvid=",
		];
	}
	
	/**
	 * 本地文件使用内存打开句柄
	 * @param  string $fileName 文件路径
	 * @return resource         句柄
	 */
	public function utf8_fopen_read($fileName)
	{ 
		$fc = file_get_contents($fileName);
		$handle = fopen("php://memory", "rw"); 
		fwrite($handle, $fc); 
		fseek($handle, 0); 
		return $handle; 
	} 
	
	/*
	------------------------------------------------
	| 列表
	------------------------------------------------
	*/
	
	/**
	 * 下载表格.xls
	 *
	 * 2,3,1
	 */
	public function downloadExcel()
	{
		$cookie = isset($_SESSION['cookie']) ? trim($_SESSION['cookie']) : '';
		$http_header = ['X-HTTP-Method-Override: GET'];
		$http_header[] = 'Cookie: ' . $cookie;
		$size = $this->putFileCurl($http_header, $this->bill, $this->attr['page']);
		if (!$size) {
			return ['code' => 1, 'msg' => 'login'];
			print_r([__FILE__, __LINE__]);exit;
		}
		
		$common_url = $this->api_host . '/robot/alimama/download/excel?debug&type=json&bill=1';
		if (2 == $this->bill) {
			$common_url = $this->api_host . '/robot/alimama/download/excel?debug&type=json&bill=3';
		}
		
		$code = 0;
		$msg = '';		
		switch ($this->bill) {
			case 3:
				$msg = $common_url;
				break;
			case 2:
				$msg = $common_url;
				break;
			default:
				$msg = $this->api_host . '/csv.php?debug&bill=3';
		}
		
		return array(
			'code' => $code,
			'msg' => $msg,
            'result' => $size,
            'pageCount' => 1,
        );
	}
	
	/**
	 * 解析表格.csv
	 *
	 * 2,1,3
	 */
	public function parseExcel()
	{
		$Excel = new AlimamaChoiceExcel;
		
		$offset = $this->attr['limit'] - 1;
		$bill = $this->bill;
		$key = 0;
		$path = $this->getProp($key, 'paths');
		# $path = $this->cache_dir . "/$bill.csv";
		
		$count = 500;
		$max = $offset + $count;
		$max_url = '';
		$juhuasuan_url = $this->api_host . '/robot/alimama/parse/excel?debug&type=json&bill=3';
		$category_url = $this->api_host . '/robot/alimama/parse/category?debug&type=json';
		if (2 == $bill) {
			$category_url = $this->api_host . '/robot/alimama/parse/excel?debug&type=json&bill=1'; # 
		}
		
		// 清单列名
		$keys = [
			[],
			[
				'item', 
				'name', 
				'pic', 
				'url', 
				'class', 
				'taobaoke', 
				'price', 
				'sale', 
				'ratio', 
				'commission', 
				'wangwang', 
				'seller', 
				'shop', 
				'platform', 
				'coupon', 
				'total', 
				'remain', 
				'denomination', 
				'start', 
				'end', 
				'link', 
				'promotion'
			],
			[
				'class', 
				'shop', 
				'platform', 
				'item', 
				'name', 
				'url', 
				'pic', 
				'price', 
				'ratio', 
				'begin', 
				'denomination', 
				'cost',
				'total', 
				'remain', 
				'start', 
				'end', 
				'promotion', 
				'note'
			],
			[
				'item', 
				'name', 
				'price', 
				'cost', 
				'group', 
				'pic', 
				'start', 
				'end', 
				'total', 
				'sale', 
				'remain', 
				'taobaoke', 
				'url', 
				'ratio', 
				'commission', 
				'coupon', 
				'class'
			],
		];		
		$key = isset($keys[$bill]) ? $keys[$bill] : [];
		$num = count($key);
		# print_r($key);
		
		/* 解析 CSV 文件 */
		$row = 0;
		$result = [];
		# fopen($path, "r,ccs=utf-8")
		if (($handle = $this->utf8_fopen_read($path)) !== false) {
			# while (($data = fgetcsv($handle, 0, ",")) !== false) {
			while (($buffer = fgets($handle, 4096)) !== false) {
				
				if ($offset < $row && $row < $max) {
					$data = explode(',', trim($buffer));
					/* 列数检测 */
					$num_data = count($data);
					if ($num_data != $num) {
						continue;
						print_r([__METHOD__, __LINE__, __FILE__, $key, $data]);exit; #
					}
				
					/* 列名转键名 */					
					$arr = ['choice_id' => $bill];
					for ($c = 0; $c < $num; $c++) {
						$keyname = isset($key[$c]) ? $key[$c] : $c;
						$datum = null;
						if (isset($data[$c])) {
							$datum = trim($data[$c]);
						} else {
							print_r($data);exit; #
						}
						
						// 编码
						if ($this->csv_encoding && 'utf-8' != $this->csv_encoding) {
							$datum = mb_convert_encoding($datum, 'utf-8', $this->csv_encoding);# 
						}
						$arr[$keyname] = $datum;
					}
					

					/* 匹配优惠券信息 */
					if (isset($arr['denomination'])) {
						if (preg_match("/满(\d+)元减(\d+)元/", $arr['denomination'], $matches)) {
							$arr['full'] = $matches[1];
							$arr['discount'] = $matches[2];
							if ($arr['full'] <= $arr['price']) {
								$arr['cost'] = $arr['price'] - $arr['discount'];
							}
							
						} elseif (preg_match("/(\d+)元无条件券/", $arr['denomination'], $matches)) {
							$arr['discount'] = $matches[1];
							# $arr['cost'] = round($arr['price'] - $arr['discount'], 2);
							$arr['cost'] = (double) $arr['price'] - (double) $arr['discount'];
						}
					}
					
					/* 后续修复处理 */
					if (1 == $bill) {
						if (!isset($arr['cost'])) {
							$arr['cost'] = $arr['price'];
						}
						
					} elseif (2 == $bill) {
						$arr['sale'] = $arr['total'] - $arr['remain'];
						$url = parse_url($arr['promotion'], PHP_URL_QUERY);
						parse_str($url, $query);
						# print_r($query);exit;
						$arr['coupon'] = isset($query['e']) ? $query['e'] : $arr['start'] .'_'. $arr['end'];
						
					} elseif (3 == $bill) {
						$arr['discount'] = $arr['price'] - $arr['cost'];
					}
					
					// 开始结束时间
					if ($arr['start'] && !preg_match("/:/", $arr['start'])) {
						$arr['start'] .= ' 00:00:00';
					}
					if ($arr['end'] && !preg_match("/:/", $arr['end'])) {
						$arr['end'] .= ' 23:59:59';
					}
					# print_r($arr);exit; 

					/* 检查条目 */
					# $Excel->return = 'update.status';
					# print_r($arr);exit;
					$check = $Excel->exist($arr); #, 'data'
					# print_r($check);exit;
					$check = is_numeric($check) ? $check : $check; #'update'
					$result[$row] = [$arr['item'], $check];
				} elseif ($row == $max) {
					$max_url = "$this->api_host/robot/alimama/parse/excel?debug&type=json&bill=$bill&limit=$max";
					break;
				}
				$row++;
			}
			fclose($handle);
		}
		
		/* 接力任务 */
		$code = 0;
		$msg = '';		
		switch ($bill) {
			case 3:
				# $code = 1;
				$msg = $max_url ? : $category_url;
				break;
			case 2:
				$msg = $max_url ? : $category_url;
				break;
			default:
				if (count($result)) {
					$msg = $max_url ? : $juhuasuan_url;
				} else {
					$msg = $juhuasuan_url;
				}
		}
		
		/* 返回数据 */
		return [
			'code' => $code,
			'msg' => $msg,
			'result' => $result,
			'pageCount' => 1,
        ];
	}
	
	/**
	 * 逆向更新
	 *
	 */
	public function updateList()
	{
		$page = $this->attr['page'];
		$limit = 10;
		$offset =  $page * $limit - $limit;
		
		$Excel = new AlimamaChoiceExcel;
		$Category = new AlimamaProductCategory;
		$List = new AlimamaChoiceList;
		
		$where = "`pic` = 'http://img.alicdn.com/bao/uploaded/i1/2863563282/TB2lNkaFAOWBuNjSsppXXXPgpXa_!!2863563282-0-item_pic.jpg'";
		$where = [];
		$column = '*';
		$option = ['list_id', "$offset,$limit"];
		$join = null;
		$all = $List->_select($where, $column, $option, null, $join);
		$count = $List->count($where);
		$pageCount = ceil($count / $limit);
		
		$result = [];
		$time = time();
		foreach ($all as $key => $row) {
			$arr = $Excel->sel("excel_id = $row->excel_id", '*');			
			$url = $arr->taobaoke;
			$link = $arr->promotion;
			$price = $arr->cost;
			if (0 < $arr->group) {
				$link = $arr->url;
			}			
			if (0 > $price) {
				$price = $arr->price;
			}
			$ar = [
				'title' => $arr->name,
				'pic' => $arr->pic,
				'url' => $url,
				'link' => $link,
				'sold' => $arr->sale,
				'cost' => $arr->price,
				'price' => $price,
				'save' => $arr->discount,
				'start' => $arr->start,
				'end' => $arr->end,
			];
			
			/* 比较 */
			$diff = $List->array_diff_kv($ar, (array) $row);			
			$data = [];
			foreach ($diff as $k => $value) {
				$data[$k] = $value[0];
			}
			/* 更新 */
			if ($data) {
				$data['updated'] = $time;
				$res = $List->set([$data, $row->list_id]);
				$result[] = $res[0];
			}
		}
		
		/* 接力任务 */		
		$msg = '';
		$code = 0;
		if ($page < $pageCount) {
			$page++;
			$msg = "http://lan.urlnk.com/robot/alimama/update/list?debug&type=json&page=$page";
		} else {
			$code = 1;
		}
		
		/* 返回数据 */
		$result = array(
            'result' => $result,
			'pageCount' => $pageCount,
			'msg' => $msg,
			'code' => $code,
        );		
		return $result;
	}
	
	/**
	 * 最后创建和编辑的时间
	 */
	public function where_optimizeTime()
	{
		$List = new AlimamaChoiceList;
		$time = isset($_SESSION['optimize_time']) ? $_SESSION['optimize_time'] : null;
		if (null === $time) {
			$_SESSION['optimize_time'] = $time = $List->getLastUpdated();
		}
		
		if (!$time) {
			return '';
		}
		return $where = "modified > $time OR created > $time";
	}
	
	/**
	 * 优化列表
	 *
	 * 原始列表更新到精简列表
	 */
	public function optimizeList()
	{
		$page = $this->attr['page'];
		$limit = 10;
		$offset =  $page * $limit - $limit;
		
		$Excel = new AlimamaChoiceExcel;
		$Category = new AlimamaProductCategory;
		$List = new AlimamaChoiceList;
		
		/* 取出列表 */
		$where = $this->where_optimizeTime();
		# $column = 'alimama_choice_excel.*, B.category_id';
		$column = '*';
		$column = 'class,taobaoke,promotion,cost,price,`group`,url,platform,excel_id,item,name,pic,sale,discount,`start`,end';
		$option = ['excel_id ASC', "$offset,$limit"];
		# $join = 'LEFT JOIN com_urlnk.alimama_product_category B ON B.title = alimama_choice_excel.class';
		$join = null;
		# print_r([$where, $column, $option]);
		$all = $Excel->_select($where, $column, $option, null, $join);
		# print_r($all); exit;
		$count = $Excel->count($where);
		$pageCount = ceil($count / $limit);
		
		/* 分类 */
		$categories = [];
		$cat = [];
		foreach ($all as $key => $row) {
			if ($row->class && !in_array($row->class, $cat)) {
				$cat[] = $row->class;
			}
		}
		if ($cat) {
			$cat = implode("', '", $cat);
			$where = "title IN('$cat')";
			$cats = $Category->_select($where, 'category_id,title');
			foreach ($cats as $c) {
				$categories[$c->title] = $c->category_id;
			}
		}
		# print_r($categories);
		
		/* 检测列表 */
		$result = [];
		foreach ($all as $key => $row) {
			$url = $row->taobaoke;
			$link = $row->promotion;
			$price = (0 >= $row->cost) ? $row->price : $row->cost;
			$site = 1;
			$category_id = isset($categories[$row->class]) ? $categories[$row->class] : 0;
			if (0 < $row->group) {
				$link = $row->url;
				$site = 3;
			} elseif ('天猫' == $row->platform) {
				$site = 2;
			}
			
			$arr = [
				'excel_id' => $row->excel_id,
				'item_id' => $row->item,
				'category_id' => $category_id,
				'title' => $row->name,
				'pic' => $row->pic,
				# 'url' => $url,
				# 'link' => $link,
				'site' => $site,
				'sold' => $row->sale,
				'cost' => $row->price,
				'price' => $price,
				'save' => $row->discount,
				'start' => $row->start,
				'end' => $row->end,
			];
			$result[] = $List->exist($arr);
		}
		unset($all);
		# print_r($all);
		# print_r($result);
		
		/* 接力任务 */
		$code = 0;
		$msg = '';
		if ($page < $pageCount) {
			$page++;
			$msg = "$this->api_host/robot/alimama/optimize/list?debug&type=json&page=$page";
		} else {
			# $code = 1;
			$msg = "$this->api_host/robot/alimama/optimize/category?debug&type=json";
			unset($_SESSION['optimize_time']);
			
		}
		
		/* 返回数据 */
		return [
			'code' => $code,
			'msg' => $msg,
            'result' => $result,
			'pageCount' => $pageCount,			
		];
	}
	
	/*
	------------------------------------------------
	| 分类
	------------------------------------------------
	*/
	
	/**
	 * 解析分类
	 *
	 * 列表分类是否都存在分类表中
	 */
	public function parseCategory()
	{
		$Excel = new AlimamaChoiceExcel;
		$Category = new AlimamaProductCategory;
		$arr = [];

		// 聚划算分类
		$classes = $Excel->classIds();		
		foreach ($classes as $class) {
			$row = [
				'title' => $class->class,
				'class_id' => $class->coupon,
			];
			$arr[] = $Category->exist($row);
		}
		
		// 其他分类
		$classes = $Excel->classIds('=');
		foreach ($classes as $class) {
			$row = [
				'title' => $class->class,
			];
			$arr[] = $Category->exist($row);
		}

		/* 接力任务 */
		$code = 0;
		$msg = "$this->api_host/robot/alimama/optimize/list?debug&type=json";
		
		/* 返回数据 */
		return [
			'code' => $code,
			'msg' => $msg,
            'result' => $arr,
			'pageCount' => 1,			
		];
	}
	
	/**
	 * 优化分类
	 *
	 * 更新商品分类数目
	 */
	public function optimizeCategory()
	{
		$List = new AlimamaChoiceList;
		$Category = new AlimamaProductCategory;
		$time = time();
		$update = $Category->update(['total' => 0, 'updated' => $time]);
		$result = [];
		
		// 更新子类
		$catNum = $List->categoryNum();
		foreach ($catNum as $c) {
			$result[$c->category_id] = $Category->update(['total' => $c->num, 'updated' => $time], ['category_id' => $c->category_id]);
		}
		
		// 更新主类
		$cat = $Category->rootNum();
		foreach ($cat as $r) {
			$result[$r->upper_id] = $Category->update(['total' => $r->num, 'updated' => $time], $r->upper_id);
		}
		
		/* 返回数据 */
		return [
			'code' => 1,
			'msg' => 'final',
            'result' => $result,
			'pageCount' => 1,
        ];
	}
	
	public function getCookieRow($key, $cookie = null)
	{
		$arr = preg_split('/;\s+/', $cookie);
		$token = '';
		foreach ($arr as $row) {
			$itm = preg_split('/=/', $row, 2);
			if ($key == $itm[0]) {
				$token = $itm[1];
				goto a;
			}
		}
		
		a:
		return $token;
	}
	
	public function downloadCoupon($url = null)
	{
		# $url = 'https://m.tb.cn/h.3gF2Zlo';
		
		$cookie = isset($_SESSION['taobao_cookie']) ? trim($_SESSION['taobao_cookie']) : '';
		$token = $this->getCookieRow('_m_h5_tk', $cookie);
		$exp = explode('_', $token);
		$token = $exp[0];
		
		$item = null;
		$encoding = $this->getCouponEncoding($url);
		if (!is_numeric($encoding)) {
			$data = [
				'e' => $encoding,
				'pid' => 'mm_33543472_5896322_20676495',
			];
			$json = json_encode($data);
			
			$time = time();
			$time .= mt_rand(100, 999);	
			
			$sign = $this->getCouponSign($token, $json, $time);
			$info = $this->getCouponInfo($time, $sign, $json);
			# print_r(get_defined_vars());
			
			$file = 'tmp/tb/' . md5($url) . '.json';
			if (file_exists($file)) {
				$data = file_get_contents($file);
			} else {
				$http_header = ['X-HTTP-Method-Override: GET'];
				$http_header[] = 'Cookie: ' . $cookie;
				$curl = new PhpCurl($info);
				$data = $curl->download($http_header);
				file_put_contents($file, $data);
			}
			$obj = json_decode($data);
			#print_r($obj);
			@$item = $obj->data->result->item->itemId;
		} else {
			$item = $encoding;
		}
		
		if ($item) {
			$cookie = isset($_SESSION['cookie']) ? trim($_SESSION['cookie']) : '';		
			$obj = $this->getSearchJson($item, $cookie);
			@$total = $obj->data->paginator->items;
			if ($total) {
				$list = $obj->data->pageList;
				$token = $this->getCookieRow('_tb_token_', $cookie);
				$obj = null;
				foreach ($list as $row) {
					$item = $row->auctionId;
					$rate = $row->tkCommonRate;
					$fee = $row->tkCommonFee;
					$price = $row->zkPrice;
					$amount = $row->couponAmount;
					$title = $row->title;
					if ($row->couponStartFee) {
						if ($price >= $row->couponStartFee) {
							$price -= $amount;
						}
					}
					$url = $this->getAuctionCode($item, $token);
					$obj = $this->getAuctionJson($item, $url, $cookie);
					break;
				}
				# print_r($obj);
				if ($obj) {
					return $arr = [
						'code' => 200,
						'command' => $obj->data->couponLinkTaoToken,
						'url' => $obj->data->couponShortLinkUrl,
						'rate' => $rate,
						'fee' => $fee,
						'amount' => $amount,
						'price' => $price,
						'title' => $title,
					];
					print_r($arr);
				}
			}
		}
		
		/* 返回数据 */
		return [
			'code' => 1,
			'msg' => 'final',
            'result' => $item,
			'pageCount' => 1,
        ];
	}
	
	public function testIndex()
	{
		$url = 'https://a.m.taobao.com/i570312752843.htm?price=16.9&original_price=58&sourceType=item&sourceType=item&suid=279b02c1-9221-4e8a-b2ed-d732051bb9f3&ut_sk=1.W6H7g5JZO4MDABgbzceLrSdC_21646297_1537427848933.Copy.1&un=72db47e31d33390e4b68d5f77a161062&share_crt_v=1&sp_tk=77+lMThnQ2I0WG9nYVDvv6U=&cpp=1&shareurl=true&spm=a313p.22.170.972569079926&short_name=h.3TTCFew';
		$url = 'https://m.tb.cn/h.3TTCFew';
		$encoding = $this->getCouponEncoding($url);
		
        return [
            'code' => 1,
            'msg' => 'final',
            'result' => $encoding,
            'pageCount' => 1,
        ];
	}
	
	public function getAuctionJson($item, $url = null, $cookie = null)
	{
		$file = 'tmp/tb/' . $item . '.txt';
		if (file_exists($file)) {
			$data = file_get_contents($file);
		} else {
			$http_header = ['X-HTTP-Method-Override: GET'];
			$http_header[] = 'Cookie: ' . $cookie;
			$curl = new PhpCurl($url);
			$data = $curl->download($http_header);
			file_put_contents($file, $data);
		}
		return $obj = json_decode($data);
	}
	
	public function getSearchJson($item, $cookie = null)
	{
		$file = 'tmp/tb/' . $item . '.json';
		if (file_exists($file)) {
			$data = file_get_contents($file);
		} else {
			$url = 'https://item.taobao.com/item.htm?id=' . $item;
			$url = 'https://pub.alimama.com/items/search.json?q=' . urlencode($url);
			
			
			$http_header = ['X-HTTP-Method-Override: GET'];
			$http_header[] = 'Cookie: ' . $cookie;
			$curl = new PhpCurl($url);
			$data = $curl->download($http_header);
			file_put_contents($file, $data);
		}
		return $obj = json_decode($data);
		# print_r($obj);
	}
	
	public function getCouponEncoding($url = 'https://m.tb.cn/h.3gF2Zlo')
	{
		$file = 'tmp/tb/' . md5($url) . '.html';
		if (file_exists($file)) {
			$content = file_get_contents($file);
		} else {
			$content = file_get_contents($url);
			file_put_contents($file, $content);
		}

		$url = '';
		if (preg_match("/var url = '(.*)';/", $content, $matches)) {
			# print_r($matches);
			$url = $matches[1];
			if (preg_match('/http(|s):\/\/a\.m\.taobao\.com\/i(\d+)\.htm/i', $url, $matche_url)) {
				return $matche_url[2];
				# print_r($matche_url);
			}
			
			# $parse_url = parse_url($url);
			$query_string = parse_url($url, PHP_URL_QUERY);
			parse_str($query_string, $query_data);
			# print_r([$parse_url, $query_data]);exit;
			$url = isset($query_data['e']) ? $query_data['e'] : '';
			$url = $url ? : (isset($query_data['id']) ? $query_data['id'] : '');
		}
		return $url;
	}
	
	public function getCouponInfo($time, $sign = null, $json = null)
	{
		$url = 'https://acs.m.taobao.com/h5/mtop.alimama.union.hsf.coupon.get/1.0/?';	
		$query_data = [
			'jsv' => '2.4.0',
			'appKey' => 12574478,
			't' => $time,
			'sign' => $sign,
			'api' => 'mtop.alimama.union.hsf.coupon.get',
			'v' => '1.0',
			#'AntiCreep' => 'true',
			#'AntiFlood' => 'true',
			'type' => 'json',
			#'dataType' => 'jsonp',
			#'callback' => 'mtopjsonp1',
			'data' => $json,
		];
		
		$query_string = http_build_query($query_data);
		return $url .= $query_string;
	}
	
	public function getAuctionCode($item, $token = null)
	{
		$url = 'https://pub.alimama.com/common/code/getAuctionCode.json?';
		$query_data = [
			'auctionid' => $item,
            'adzoneid' => 20676495,
            'siteid' => 5896322,
            'scenes' => 1,
            #'tkFinalCampaign' => 20,
            #'t' => 1537251775365,
            #'_tb_token_' => $token,
            #'pvid' => '10_36.33.151.1_596_1537251735163',
		];
		
		$query_string = http_build_query($query_data);
		return $url .= $query_string;
	}
	
	public function getCouponSign($_m_h5_tk, $data = null, $t = null)
	{
		if (is_array($data)) {
			$data = json_encode($data);
		}
		$appKey = 12574478;
		$str = "$_m_h5_tk&$t&$appKey&$data";
		return md5($str);		
	}
}
