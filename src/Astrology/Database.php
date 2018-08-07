<?php

namespace Astrology;

class Database
{
	public static $adapter = null;
	public $driver = 'pdo_mysql';
	public $host = 'localhost';
	public $port = 3306;
	public $user = 'root';
	public $password = 'root';
	public $db_name = 'mysql';
	public $table_name = 'user';
	public $primary_key = null;
	public $join = '';
	public $group_by = '';
	public $having = '';
	
	public $return = null;
	
	public function __construct($arg = [])
	{
		if (!$arg) {
			$arg = $GLOBALS['CONFIG']['database'];
		}
		$this->init($arg);
		$this->_init();
	}
	
	public function setVar($arg = [])
	{
		foreach ($arg as $key => $value) {
			$this->$key = $value;
		}
	}
	
	public function init($arg = [])
	{
		$this->setVar($arg);
		$arg = [
			'host' => $this->host,
			'port' => $this->port,
			'db_name' => $this->db_name,
			'username' => $this->user,
			'password' => $this->password,
		];
		$this->getAdapter($this->driver, $arg);
	}
	
	public function _init()
	{
		
	}
	
	public function getAdapter($name, $arg)
	{
		$name = str_replace('_', ' ', $name);
		$name = ucwords($name);
		$name = str_replace(' ', '', $name);
		$class = '\Astrology\Extension\Php' . $name;
		return self::$adapter = new $class($arg);
	}
	
	public function query($sql = null)
	{
		return self::$adapter->query($sql);
	}
	
	public function from($name = null)
	{
		return $name = $name ? : $this->db_name . '.' . $this->table_name;
	}
	
	public function sqlSet($data)
	{
		# print_r($data);exit;
		
		if (!is_array($data)) {
			return $data;
		}
		
		$arr = [];
		foreach ($data as $key => $value) {
			if (null !== $value) {
				$value = addslashes($value);
				$arr []= "`$key` = '$value'";
			} else {
				$arr []= "`$key` = NULL";
			}
			
		}
		return $str = implode(", ", $arr);
	}
	
	public function sqlWhere($where, $type = 'AND')
	{
		if (!is_array($where)) {
			return $where;
		}
		
		$arr = [];
		foreach ($where as $key => $value) {
			// 没有列名的直接写SQL语句
			if (is_numeric($key)) {
				$arr[] = $value;
				
			// 多条件
			} elseif (preg_match('/^(ADN|OR)$/', $key, $matches)) {
				print_r([$matches, __FILE__, __LINE__]);
				exit;
			} elseif (is_array($value)) {
				print_r([$value, __FILE__, __LINE__]);
				exit;
			} else {
				$value = addslashes($value);
				$arr[] = "`$key` = '$value'";
			}
		}
		return $str = implode(" $type ", $arr);
	}
	
	public function insert($data)
	{
		$db_table = $this->from();
		$sql = "INSERT INTO $db_table SET ";
		$sql .= $this->sqlSet($data);
		return self::$adapter->insert($sql);
	}
	
	/**
	 * 批量插入
	 *
	 */
	public function into($field = null, $value = null)
	{
		# print_r([$field, $value]);exit; 
		
		$db_table = $this->from();
		
		if (is_string($field)) {
			$field = explode(',', $field);
		}
		if (is_array($value) && $value) {
			
		} else {
			$value = [[]];
		}
		
		$count = count($field);
		$field = implode('`, `', $field);
		$field = '(`' . $field . '`)';
		
		$values = [];
		foreach ($value as $row) {
			$arr = [];
			for ($i = 0; $i < $count; $i++) {
				$val = isset($row[$i]) ? $row[$i] : null;
				if (null != $val) {
					$val = addslashes($val);
					$val = "'$val'";
				} else {
					$val = 'NULL';
				}
				$arr []= $val;
			}
			$data = implode(', ', $arr);
			$data = '(' . $data . ')';
			$values []= $data;
		}
		$values = implode(', ', $values);
		
		$sql = "INSERT INTO $db_table $field VALUES ";
		$sql .= $values;
		if ('into.sql' == $this->return) {
			return $sql;
		}
		return self::$adapter->insert($sql);
	}
	
	public function find($where = null, $column = '*', $order = null, $limit = 1)
	{
		$db_table = $this->from();
		$sql = "SELECT $column FROM $db_table";
		$where = $this->sqlWhere($where);
		if ($where) {
			$sql .= " WHERE $where";
		}
		if ($order) {
			$sql .= " ORDER BY $order";
		}
		$sql .= " LIMIT $limit";#  echo $sql;exit;
		if ('find.sql' == $this->return) {
			return $sql;
		}
		return self::$adapter->find($sql);
	}
	
	public function sel($where = null, $column = null, $order = null, $group = [], $join = null)
	{
		$column = $column ? : ($this->primary_key ? : '*');
		return $this->find($where, $column, $order);
	}
	
	public function count($where = null)
	{
		$db_table = $this->from();
		$sql = "SELECT COUNT(0) AS num FROM $db_table";
		$where = $this->sqlWhere($where);
		if ($where) {
			$sql .= " WHERE $where";
		}
		$row = self::$adapter->find($sql);
		return $row->num;
	}
	
	public function select($where = null, $column = '*', $order = null, $limit = 10, $offset = null)
	{
		$db_table = $this->from();
		$sql = "SELECT $column FROM $db_table";
		if ($this->join) {
			$sql .= " $this->join";
		}
		
		$where = $this->sqlWhere($where);
		if ($where) {
			$sql .= " WHERE $where";
		}
		
		if ($this->group_by) {
			$sql .= " GROUP BY $this->group_by";
			if ($this->having) {
				$sql .= " HAVING $this->having";
			}
		}
		
		if ($order) {
			$sql .= " ORDER BY $order";
		}
		if ($limit) {
			$sql .= " LIMIT $limit";
		}
		if (null !== $offset) {
			$sql .= " OFFSET $offset";
		}
		return self::$adapter->select($sql);
	}
	
	public function _select($where = null, $column = null, $option = [], $group = [], $join = [])
	{
		$order = null;
		$limit = 10;
		
		/* 排序和条目 */
		if (isset($option[0])) {
			$order = $option[0];
			if (isset($option[1])) {
				$limit = $option[1];
			}
		}
		
		/* 分组 */
		if (isset($group[0])) {
			$this->group_by = $group[0];
			if (isset($group[1])) {
				$this->having = $group[1];
			}
		}
		
		/* 连接 */
		if ($join) {
			$this->join = $join;
		}
		
		return $this->select($where, $column, $order, $limit);
	}
	
	public function update($set = [], $where = null, $order = null, $limit = null)
	{
		$db_table = $this->from();
		$sql = "UPDATE $db_table SET ";
		$sql .= $this->sqlSet($set);
		
		$condition = '';
		$whereSql = $this->sqlWhere($where);
		if ($whereSql) {
			$whereSql = is_numeric($whereSql) ? "`$this->primary_key` = $whereSql" : $whereSql;
			$condition .= " WHERE $whereSql";
		}
		
		if ($order) {
			$condition .= " ORDER BY $order";
		}
		if (null !== $limit) {
			$condition .= " LIMIT $limit";
		}
		$sql .= $condition;
		if ('update.sql' == $this->return) {
			return $sql;
		}
		$exec = $this->exec($sql);
		/*
		if ('update.status' == $this->return) {
			if ($exec) {
				return $row = $this->sel($where, '*', $order);
			}
		}
		*/
		return $exec;
	}
	
	public function set()
	{
		/*
		$arr = [];
		$num = func_num_args();
		for ($i = 0; $i < $num; $i++) {
			$arg = func_get_arg($i);
			$arr []= $arg;
		}
		*/
		$arr = func_get_args();
		# print_r($arr);exit; 
		
		$result = [];
		foreach ($arr as $row) {
			# $count = count($row);
			$set = isset($row[0]) ? $row[0] : [$this->primary_key => null];
			$where = isset($row[1]) ? $row[1] : null;
			$order = isset($row[2]) ? $row[2] : null;
			$limit = isset($row[3]) ? $row[3] : null;
			$result []= $this->update($set, $where, $order, $limit);
		}
		return $result;
	}
	
	public function __call($name, $arguments)
	{
		return self::$adapter->$name($arguments[0]);
	}
}
