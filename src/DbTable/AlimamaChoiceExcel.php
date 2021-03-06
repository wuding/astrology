<?php
namespace DbTable;


class AlimamaChoiceExcel extends \Astrology\Database
{
	public $db_name = 'shopping';
	public $table_name = 'alimama_choice_excel';
	public $primary_key = 'excel_id';
	
	/**
	 * 检查条目是否存在
	 *
	 *
	 */
	public function exist($arr, $return = null)
	{
		# $this->return = 'into.sql'; 
		
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
			# $this->return = 'into.sql'; 
			$last_id = $this->into($field, [$value]);
			# print_r($last_id);exit; 
			return $last_id;
		}
		
		/* 更新 */
		if (isset($arr['cost'])) {
			$arr['cost'] = (string) $arr['cost'];
		}
		if (isset($arr['discount'])) {
			$arr['discount'] = (string) $arr['discount'];
		}
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
	
	/**
	 * 获取分类
	 *
	 *
	 */
	public function classIds($type = '>')
	{
		$column = '`class`, `coupon`';
		$where = "`group` $type 0";
		$option = ['`excel_id`', 200];
		$group = ['class'];
		return $classes = $this->_select($where, $column, $option, $group);
	}
}
