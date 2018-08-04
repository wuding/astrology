<?php
/**
 * 阿里妈妈
 *
 *
 */
namespace Plugin\Robot;

class Alimama extends \Plugin\Robot
{
	public $site_id = null;
	
	public function _init()
	{
		$this->bill = isset($_GET['bill']) ? $_GET['bill'] : 1;
		
		$this->cache_dir = CACHE_ROOT . '/http/www.alimama.com';
		
		$this->paths = array(
			$this->cache_dir . "/{$this->bill}.csv",
            $this->cache_dir . "/{$this->bill}.xls",
			"",
			$this->cache_dir . "/{$this->bill}.xls",
			$this->cache_dir . "/4/%1.json",
        );
		
		$this->urls = array(
			"",
			"https://pub.alimama.com/coupon/qq/export.json?adzoneId=126228652&siteId=35532130",
			"",
			"https://pub.alimama.com/operator/tollbox/excelDownload.json?excelId=JUPINTUAN_LIST&adzoneId=126228652&siteId=35532130",
			"https://pub.alimama.com/items/channel/qqhd.json?channel=qqhd&toPage=%1&sortType=13&dpyhq=1&perPageSize=50&shopTag=dpyhq&t=1531379311587&_tb_token_=&pvid=",
			'reurl' => 'http://www.jijizy.com/vod/?%1.html',			
        );
	}
	
	public function parseExcel()
	{
		$AlimamaChoiceExcel = new \DbTable\AlimamaChoiceExcel;
		
		$offset = $this->attr['limit'] - 1;
		$bill = $this->bill;
		$key = 0;
		# $path = $this->getProp($key, 'paths', $_1, $_2);
		$path = $this->cache_dir . "/{$this->bill}.csv";
		
		$count = 500;
		$max = $offset + $count;
		$max_url = '';
		
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
		if (($handle = fopen($path, "r")) !== FALSE) {
			while (($data = fgetcsv($handle, 0, ",")) !== FALSE) {
				
				if ($offset < $row && $row < $max) {
					/* 列数检测 */
					$num_data = count($data);
					if ($num_data != $num) {
						  print_r([__METHOD__, __LINE__, __FILE__, $key, $data]);exit;#
					}
				
					/* 列名转键名 */					
					$arr = [];
					for ($c = 0; $c < $num; $c++) {
						$datum = null;
						if (isset($data[$c])) {
							$datum =$data[$c];
						} else {
							print_r($data);exit;#
						}
						$keyname = isset($key[$c]) ? $key[$c] : $c;
						$datum = mb_convert_encoding($datum, 'utf-8', 'gbk');# 
						$arr[$keyname] = $datum;
					}
					$arr['choice_id'] = $bill;
					
					$arr['cost'] = $arr['price'];
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
							$arr['cost'] = $arr['price'] - $arr['discount'];
						}
					}
					
					/* 后续修复处理 */
					if (1 == $bill) {
						
						
					} elseif (2 == $bill) {
						$arr['sale'] = $arr['total'] - $arr['remain'];
						$url = parse_url($arr['promotion'], PHP_URL_QUERY);
						parse_str($url, $query);
						# print_r($query);exit;
						$arr['coupon'] = isset($query['e']) ? $query['e'] : $arr['start'] .'_'. $arr['end'];
						
					} elseif (3 == $bill) {
						$arr['discount'] = $arr['price'] - $arr['cost'];
					}
					
					if ($arr['end'] && !preg_match("/:/", $arr['end'])) {
						$arr['end'] .= ' 23:59:59';
					}
						
					$check = '';
					# $AlimamaChoiceExcel->return = 'update.status';
					$check = $AlimamaChoiceExcel->exist($arr); #, 'data'
					# print_r($check);exit;
					$result [$row]= array($num, $arr['item'], is_numeric($check) ? $check : $check); #'update'
					
					# print_r($arr);exit;
				} elseif ($row == $max) {
					$max_url = "http://lan.urlnk.com/robot/alimama/parse/excel?debug&type=json&bill=$bill&limit=$max";
					break;
				}
				$row++;
			}
			fclose($handle);
		}
		
		/* 接力任务 */
		$msg = '';
		$code = 0;
		$juhuasuan_url = 'http://lan.urlnk.com/robot/alimama/parse/excel?debug&type=json&bill=3';
		switch ($bill) {
			case 3:
				$code = 1;
				$msg = $max_url;
				break;
			default:
				if (count($result)) {
					$msg = $max_url ? : $juhuasuan_url;
				} else {
					$msg = $juhuasuan_url;
				}
		}
		
		/* 返回数据 */
		$result = array(
            'result' => $result,
			'pageCount' => 1,
			'msg' => $msg,
			'code' => $code,
        );		
		return $result;
	}
	
	/**
	 * 解析分类
	 *
	 */
	public function parseCategory()
	{
		$AlimamaChoiceExcel = new \DbTable\AlimamaChoiceExcel;
		$AlimamaProductCategory = new \DbTable\AlimamaProductCategory;
		$all = $AlimamaProductCategory->rootIds();
		return $all;
		
		$classes = $AlimamaChoiceExcel->classIds();
		$arr = [];
		foreach ($classes as $class) {
			$row = [
				'title' => $class->class,
				'class_id' => $class->coupon,
			];
			$arr []= $AlimamaProductCategory->exist($row);
		}
		
		$classes = $AlimamaChoiceExcel->classIds('=');
		# $arr = [];
		foreach ($classes as $class) {
			$row = [
				'title' => $class->class,
			];
			$arr []= $AlimamaProductCategory->exist($row);
		}
		return ['result' =>  $arr, 'pageCount' => 1];
		print_r([$arr, $classes]);
	}
}
