<?php

namespace DbTable;

class VideoEntry extends \Astrology\Database
{
	public $db_name = 'xyz_yingmi';
	public $table_name = 'video_entry';
	public $primary_key = 'entry_id';
	
	public function check($data = null, $return = null)
	{
		$primary_key = $this->primary_key;
		$where = [
			'category_id' => $data['category_id'],
			'name' => $data['name'],
		];
		
		$row = $this->find($where, $primary_key);
		if (!$row) {
			$incr = [
				'status' => 1,
				'created' => time(),
			];
			$arr = array_merge($data, $incr);
			
			if ($return) {
				return $arr;
			}
			return $this->insert($arr);
		}
		return $row->$primary_key;
	}
}
