<?php

namespace DbTable;

class VideoUrl extends \Astrology\Database
{
	public $db_name = 'xyz_yingmi';
	public $table_name = 'video_url';
	public $primary_key = 'url_id';
	
	public function check($data = null, $return = null)
	{
		$primary_key = $this->primary_key;
		$where = [
			'url' => $data['url'],
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
