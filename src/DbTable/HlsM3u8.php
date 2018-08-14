<?php
namespace DbTable;

class HlsM3u8 extends \Astrology\Database
{
	public $db_name = 'video';
	public $table_name = 'hls_m3u8';
	public $primary_key = 'm3u8_id';
	
	/**
	 * 根据名称查找
	 *
	 */
	public function findByName($name)
	{
		$where = [
			'name' => $name,
		];
		return $row = $this->sel($where, '*');
	}
	
	/**
	 * 根据条件查找所有
	 *
	 */
	public function fetchAll($where = [], $column = '*', $order = 'm3u8_id DESC')
	{
		$option = [$order, 30];
		return $all = $this->_select($where, $column, $option);
	}
}
