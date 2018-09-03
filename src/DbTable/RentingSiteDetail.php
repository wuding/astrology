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
		$where = [
			'site_id' => $arr['site_id'],
			'item_id' => $arr['item_id'],
			'type' => $arr['type'],
		];
		$row = $this->sel($where, '*');
		
		if (!$row) {
			$time = time();
			$data = [
				'status' => -1,
				'created' => $time,
				'updated' => $time,
			];
			$data += $arr;
			return $this->insert($data);
		}

		/* 比较 */
		$diff = $this->array_diff_kv($arr, (array) $row);
		if ($diff) {
			$data = [];
			foreach ($diff as $key => $value) {
				$data[$key] = $value[1];
			}			
			print_r([$diff, $data]);exit;
		}
		
		return $row->detail_id;
	}
}
