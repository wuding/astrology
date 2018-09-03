<?php
namespace DbTable;

class RentingSiteArea extends \Astrology\Database
{
	public $db_name = 'classified';
	public $table_name = 'renting_site_area';
	public $primary_key = 'area_id';
	
	/**
	 * 省
	 *
	 */
	public function findProv($name, $site_id, $column = '*')
	{
		$where = [
			'title' => $name,
			'site_id' => $site_id,
		];
		return $row = $this->sel($where, $column);
	}
	
	public function provinceExists($name, $site_id)
	{
		$row = $this->findProv($name, $site_id, 'area_id');
		$time = time();
		if (!$row) {
			$data = [
				'title' => $name,
				'site_id' => $site_id,
				'type' => 1,
				'created' => $time,
				'updated' => $time,
			];
			return $this->insert($data);
		}
		return $row->area_id;
	}
	
	/**
	 * 市
	 */
	public function cityExists($where, $set = [], $column = '*')
	{
		$row = $this->sel($where, $column);
		$time = time();
		if (!$row) {
			$data = [
				'type' => 2,
				'created' => $time,
				'updated' => $time,
			];
			$data += $where + $set;
			return $this->insert($data);
		}
		return $row->area_id;
	}
	
	#! 根据条件查找所有
	public function fetchAll($where = [], $column = '*', $order = 'm3u8_id DESC')
	{
		$option = [$order, 30];
		return $all = $this->_select($where, $column, $option);
	}
}
