<?php
namespace DbTable;

class RentingSiteDetail extends \Astrology\Database
{
	public $db_name = 'classified';
	public $table_name = 'renting_site_detail';
	public $primary_key = 'detail_id';
	
	/**
	 * 检测
	 *
	 */
	public function exist($arr)
	{
		$primary_key = $this->primary_key;
		$time = time();
		
		$where = [
			'site_id' => $arr['site_id'],
			'item_id' => $arr['item_id'],
			'type' => $arr['type'],
		];
		$row = $this->sel($where, '*');
		
		if (!$row) {
			
			$data = [
				'status' => -1,
				'created' => $time,
				'updated' => $time,
			];
			$data += $arr;
			return $this->insert($data);
		}

		/* 比较 */
		$diff = $this->array_diff_kv($arr, (array) $row, ['refresh_time']);
		if ($diff) {
			$data = [];
			foreach ($diff as $key => $value) {
				$data[$key] = $value[0];
			}
			$keys = array_keys($diff);
			# print_r([$diff, $data, $keys]);exit; 
			$data['updated'] = $time;
			$data['status'] = -2;
			$data['note'] = implode(',', $keys);
			return $result = $this->update($data, $row->{$primary_key});
		}
		
		return $row->{$primary_key};
	}

	public function fetchAll($where = [], $column = '*', $order = 'detail_id ASC', $page = 1, $limit = 10)
	{
		$offset = $page * $limit - $limit;
		$option = [$order, "$offset,$limit"];
		return $all = $this->_select($where, $column, $option);
	}
}
