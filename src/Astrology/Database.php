<?php

namespace Astrology;

class Database
{
	public static $adapter = null;
	public $driver = 'pdo_mysql';
	public $host = 'localhost';
	public $port = 3306;
	public $user = null;
	public $password = null;
	public $db_name = 'mysql';
	public $table_name = 'user';
	public $primary_key = null;
	
	public function __construct($arg = [])
	{
		$this->init($arg);
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
		if (!is_array($data)) {
			return $data;
		}
		
		$arr = [];
		foreach ($data as $key => $value) {
			$value = addslashes($value);
			$arr[] = "`$key` = '$value'";
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
		$sql .= " LIMIT $limit";
		return self::$adapter->find($sql);
	}
}
