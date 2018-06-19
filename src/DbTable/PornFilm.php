<?php

namespace DbTable;

class PornFilm extends \Astrology\Database
{
	public $db_name = 'club_spa8';
	public $table_name = 'porn_film';
	public $primary_key = 'film_id';
	
	public function check($data = null, $return = null)
	{
		$primary_key = $this->primary_key;
		$where = [
			'site_id' => $data['site_id'],
			'number' => $data['number'],
			'name' => $data['name'],
		];
		
		$row = $this->find($where, "$primary_key,collect_ids");
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
			
		} elseif ($row->collect_ids && $data['collect_ids'] && $data['collect_ids'] != $row->collect_ids) {
			$arr = explode(',', $row->collect_ids);
			if (!in_array($data['collect_ids'], $arr)) {
				$arr[] = $data['collect_ids'];
				$ids = implode(',', $arr);
				return $this->update("collect_ids='$ids'", array($primary_key => $row->$primary_key));
			}
		}
		return $row->$primary_key;
	}
}
