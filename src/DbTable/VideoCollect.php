<?php

namespace DbTable;

class VideoCollect extends \Astrology\Database
{
	public $db_name = 'xyz_yingmi';
	public $table_name = 'video_collect';
	public $primary_key = 'collect_id';
	
	public function check($data = null, $return = null)
	{
		$primary_key = $this->primary_key;
		$where = [
			'site_id' => $data['site_id'],
			'detail_id' => $data['detail_id'],
			'name' => $data['name'],
			/*'category_name' => 'haha',*/
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
		
		# print_r($row);
		return $row->$primary_key;
	}
}
