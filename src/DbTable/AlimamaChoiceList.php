<?php
namespace DbTable;

class AlimamaChoiceList extends \Astrology\Database
{
	public $db_name = 'com_urlnk87';
	public $table_name = 'alimama_choice_list';
	public $primary_key = 'list_id';
	
	/**
	 * 检测条目是否存在
	 *
	 */
	public function exist($arr)
	{
		$time = time();
		$where = [
			'excel_id' => $arr['excel_id'],
		];
		$row = $this->sel($where);
		if (!$row) {
			$data = [
				'created' => $time,
			];
			$data += $arr;
			$field = array_keys($data);
			$value = array_values($data);
			return $last_id = $this->into($field, [$value]);
		}
		return $row->{$this->primary_key};
	}
	
	/**
	 * 分类条目数
	 *
	 */
	public function categoryNum()
	{
		$where = [];
		$column = 'category_id,COUNT(0) AS num';
		$option = ['category_id', 200];
		return $all = $this->_select($where, $column, $option, ['category_id']);
		print_r($all);
	}
}
