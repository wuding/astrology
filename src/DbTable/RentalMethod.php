<?php
/**
 * 租赁方式
 */
namespace DbTable;

class RentalMethod extends \Astrology\Database
{
	// 数据库定义
	public $db_name = 'classified';
	public $table_name = 'rental_method';
	public $primary_key = 'method_id';

	public function exist($arr, $lockTime = 'updated')
	{
		$primary_key = $this->primary_key;
		$time = time();

		/* 参数 */
		if (is_string($arr)) {
			$arr = [
				'title' => $arr,
			];
		}
		
		/* 查询 */
		$where = [
			'title' => $arr['title'],
		];
		$row = $this->sel($where, '*');

		/* 新建 */
		if (!$row) {
			$data = [
				'created' => $time,
				$lockTime => $time,
			];
			$data += $arr;
			return $this->insert($data);
		}

		/* 比较 */
		$diff = $this->array_diff_kv($arr, (array) $row);
		if ($diff) {
			$data = [];
			foreach ($diff as $key => $value) {
				$data[$key] = $value[0];
			}			
			$data[$lockTime] = $time;
			return $result = $this->update($data, $row->{$primary_key});
		}
		
		return $row->{$primary_key};
	}
}
