<?php
/**
 * 标签
 */
namespace DbTable;

class RentTag extends RentalMethod
{
	// 数据库定义
	public $table_name = 'rent_tag';
	public $primary_key = 'tag_id';

	public function tag_ids($str)
	{
		$arr = explode(',', $str);
		$result = [];
		foreach ($arr as $key => $value) {
			$result[] = $this->exist(['title' => $value]);
		}
		# print_r($result);exit;
		return $ids = implode(',', $result);
	}
}
