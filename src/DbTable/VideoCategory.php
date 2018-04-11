<?php

namespace DbTable;

class VideoCategory extends \Astrology\Database
{
	public $db_name = 'xyz_yingmi';
	public $table_name = 'video_category';
	public $primary_key = 'category_id';
	
	public function check($data = null, $return = null)
	{
		$primary_key = $this->primary_key;
		$row = $this->find($data, $primary_key);
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
