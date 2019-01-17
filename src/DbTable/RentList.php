<?php
/**
 * 出租列表
 */
namespace DbTable;

class RentList extends \Astrology\Database
{
	// 数据库定义
	public $db_name = 'classified';
	public $table_name = 'rent_list';
	public $primary_key = 'list_id';

	/**
	 * 获取最后更新时间
	 */
	public function getLastUpdated()
	{
		$where = 'updated > 0';
		$row = $this->sel($where, 'updated', 'updated DESC');
		if ($row) {
			return $row->updated;
		}
		return false;
	}

	public function exist($arr, $lockTime = 'updated', $ignore = [])
	{
		$primary_key = $this->primary_key;
		$time = time();

		/* 查询 */
		$where = [
			'detail_id' => $arr['detail_id'],
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
		$diff = $this->array_diff_kv($arr, (array) $row, $ignore);
		if ($diff) {
			$data = [];
			foreach ($diff as $key => $value) {
				$data[$key] = $value[0];
			}
			$keys = array_keys($diff);
			# print_r([$diff, $data, $keys]);exit; 

			$data[$lockTime] = $time;
			return $result = $this->update($data, $row->{$primary_key});
		}
		
		return $row->{$primary_key};
	}
}
