<?php
namespace DbTable;

class AlimamaChoiceCsv extends \Astrology\Database
{
	public $db_name = 'com_urlnk';
	public $table_name = 'alimama_choice_csv';
	public $primary_key = 'csv_id';
	
	/**
	 * 比较数据并提交版本库
	 *
	 *
	 */
	public function diff($arr, $row = [])
	{
		$row = (array) $row;
		# print_r([$arr, $row]);exit;
		/*
		$unset = ['status', 'created', 'updated'];
		foreach ($unset as $key) {
			if (isset($arr[$key])) {
				unset($arr[$key]);
			}
		}
		*/
		
		/* 删除键 */
		$csv = $row['csv'];
		$modified = $row['modified'];
		$excel_id = $row['excel_id'];
		unset($row['status'], $row['created'], $row['updated'], $row['modified'], $row['csv'], $row['excel_id']);#
		
		/* 比较 */
		$diff = $this->array_diff_kv($row, $arr);
		# var_dump($diff);
		$keys = array_keys($diff);
		$difference = null;
		if (!$keys) {
			return false;
		}
		$difference = implode(',', $keys);
		
		/* 检测插入 */
		$row['difference'] = $difference;
		# var_dump($row['difference']);
		$row['modified'] = $modified;
		$row['seq'] = $csv;
		$row['excel_id'] = $excel_id;
		$row['compared'] = time();
		# print_r([$arr, $row]);exit;
		return $result = $this->exist($row);
		print_r($result);
		exit;
	}
	
	public function array_diff_kv($arr = [], $other = [], $full = null)
	{
		$diff = [];
		foreach ($arr as $key => $value) {
			if (isset($other[$key])) {
				$val = $other[$key];
				if ($value != $val) {
					$diff[$key] = [$value, $val];
				}
			} else {
				# $diff[$key] = [$value];
			}
		}
		return $diff;
	}
	
	/**
	 * 检查条目是否存在
	 *
	 *
	 */
	public function exist($arr, $return = null)
	{
		# $this->return = 'into.sql';
		
		$primary_key = $this->primary_key;
		
		
		/* 检测 */
		$where = [
			'excel_id' => $arr['excel_id'],
			'seq' => $arr['seq'],
		];
		
		$row = $this->sel($where);
		# print_r($row); exit;
		
		/* 插入 */
		if (!$row) {
			if ('data' == $return) {
				return $arr;
			}
			$data = $arr;
			$field = array_keys($data);
			$value = array_values($data);
			$last_id = $this->into($field, [$value]);
			# print_r($last_id);exit;
			return $last_id;
		}
		
		/* 更新 */
		$result = $this->set([$arr, $row->{$primary_key}]);
		$result = $result[0];
		return $result;
	}
}
