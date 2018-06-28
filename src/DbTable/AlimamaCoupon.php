<?php
namespace DbTable;

class AlimamaCoupon extends \Astrology\Database
{
    public $db_name = 'com_urlnk';
	public $table_name = 'alimama_coupon';
	public $primary_key = 'id';
	
    public function __construct($arg = array())
    {
        parent::__construct($arg);
        $this->time = $_SERVER['REQUEST_TIME'];
    }
    
    public function checkRow($arr, $return = null, $find = array())
    {
		if (!isset($arr['group'])) {
			$arr['group'] = 0;
		}
		
        $w = array(
            'item' => $arr['item'],
			'group' => $arr['group'],
        );
        
        $r = $this->fetchRow($w, 'id');
        if (!$r) {
            $where['status'] = -1;
            $where['created'] = $this->time;
            $where['updated'] = $this->time;
            
            $arr = array_merge($where, $arr);
            if ($return) {
                return $arr;
            }
            return $this->from($this->db_name .".$this->table_name")->addSimple($arr);
        }
		$arr['status'] = -2;
		$arr['updated'] = $this->time;
		
		return $this->update($arr, ['id' => $r->id]);
    }
	
	public function getClass()
	{
		$where = array();
		$order = 'CONVERT(class USING gbk)';
		$this->group_by = 'class';
		$all = $this->findAll($where, $order, '0,100', 'class', 'class');
		$this->group_by = '';
		return $all;
	}
}
