<?php

namespace DbTable;

class TaobaoCommand extends \Astrology\Database
{
	public $db_name = 'com_urlnk';
	public $table_name = 'taobao_command';
	public $primary_key = 'id';

	public function exist($arr)
	{
		$primary_key = $this->primary_key;
		$time = time();
		$where = [
			'command' => $arr['command'],
		];
		$row = $this->sel($where, '*');
		# print_r([$row, $where]);exit;
		if (!$row || !isset($row->id)) {
			$data = [
				'created' => $time,
				'updated' => $time,
			];
			$data += $arr;
			$field = array_keys($data);
			$value = array_values($data);
			# $this->return = 'into.sql';
			return $last_id = $this->into($field, [$value]);
		}
		
		/* 比较 */
		$diff = $this->array_diff_kv((array) $row, $arr);
		$data = [];
		foreach ($diff as $key => $value) {
			$data[$key] = $value[1];
		}
		
		print_r([$data, $row, $arr]);exit; # 
		
		/* 更新 */
		if ($data) {
			# var_dump($diff);exit; 
			$data['updated'] = $time;
			$result = $this->set([$data, $row->{$primary_key}]);
			$result = $result[0];
			return $result;
		}
		return $row->{$primary_key};
	}
}
