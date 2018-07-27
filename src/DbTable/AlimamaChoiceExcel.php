<?php
namespace DbTable;


class AlimamaChoiceExcel extends \Astrology\Database
{
	public $db_name = 'com_urlnk';
	public $table_name = 'alimama_choice_excel';
	public $primary_key = 'excel_id';
	
	/**
	 * 检查条目是否存在
	 *
	 *
	 */
	public function exist($arr, $return = null)
	{
		# 
		
		$primary_key = $this->primary_key;
		$time = time();
		
		/* 检测 */
		$where = [
			'item' => $arr['item'],
			'coupon' => $arr['coupon'],#
		];
		$row = $this->sel($where, '*');
		
		/* 插入 */
		if (!$row) {
			
			
			$data = [
				'status' => 0,
				'created' => $time,
			];			
			$data += $arr;
			if ('data' == $return) {
				return $data;
			}
			
			$field = array_keys($data);
			$value = array_values($data);
			return $last_id = $this->into($field, [$value]);
		}
		
		/* 更新 */
		$set = $arr;
		# $set['updated'] = $time;		
		$result = $this->set([$set, $row->{$primary_key}]);
		# print_r($result); exit;
		$result = $result[0];
		
		/* 修订版本 */
		if ($result) {
			# $this->return = 'update.sql';
			$result = $this->set(["`csv` = csv + 1, `updated` = $time, `modified` = $time", $row->{$primary_key}]);
			# print_r($result); exit;
			
			$AlimamaChoiceCsv = new AlimamaChoiceCsv();
			$result = $AlimamaChoiceCsv->diff($arr, $row);
			
			return $result;
		}
		return $result;
	}
}
