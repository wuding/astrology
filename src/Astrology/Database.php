<?php

namespace Astrology;

use PDO;

class Database
{
	public static $adapter = null;
	public $driver = 'pdo_mysql';
	public $host = 'localhost';
	public $port = 3306;
	public $user = 'root';
	public $password = 'root';
	public $driver_options = null;

	public $db_name = 'mysql';
	public $table_name = 'user';
	public $primary_key = null;

	public $join = '';
	public $group_by = '';
	public $having = '';
	
	public $return = null;
	public $logs = [];
	
	public function __construct($arg = [])
	{
		if (!$arg) {
			$arg = $GLOBALS['CONFIG']['database'];
		}
		$driver_options = array(
		    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
		);
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
			'dsn_prefix' => 'mysql',
			'host' => $this->host,
			'port' => $this->port,
			'dbname' => $this->db_name,
			'unix_socket' => '/tmp/mysql.sock',
			'charset' => 'utf8mb4',
			'username' => $this->user,
			'password' => $this->password,
			'driver_options' => $this->driver_options,
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
		$class = '\Ext\Php' . $name;
		return self::$adapter = new $class($arg);
	}
	
	public function query($sql = null)
	{
		return $this->logs($sql, 'query') ? : self::$adapter->query($sql);
	}
	
	public function from($name = null)
	{
		return $name = $name ? : $this->db_name . '.' . $this->table_name;
	}

	public function sqlColumns($column = '*')
	{
		if (is_array($column)) {
			return implode(',', $column);
		}
		return $column;
	}
	
	public function sqlSet($data)
	{
		# print_r($data);exit;
		
		if (!is_array($data)) {
			return $data;
		}
		
		$arr = [];
		foreach ($data as $key => $value) {
			if (is_numeric($key)) {
				$arr[] = $value;
			} else {
				$val = 'NULL';
				if (null !== $value) {
					$value = addslashes($value);
					$val = "'$value'";
				}
				$arr[]= "`$key` = $val";
			}
		}
		return $str = implode(", ", $arr);
	}
	
	public function sqlWhere($where, $type = 'AND')
	{
		if (!is_array($where)) {
			$where = is_numeric($where) ? "`$this->primary_key` = $where" : $where;
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
			} elseif (preg_match('/^(NOT|LIKE)\s+/i', $value, $matches)) {
				$arr[] = "`$key` $value";
				# print_r([$arr, __FILE__, __LINE__]);
				# exit;
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
		return $this->logs($sql, 'insert') ? : self::$adapter->insert($sql);
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
		return $this->logs($sql, 'into') ? : self::$adapter->insert($sql);
	}
	
	public function find($where = null, $column = '*', $order = null, $limit = 1, $call = null)
	{
		$db_table = $this->from();
		$column = $this->sqlColumns($column);
		$sql = "SELECT $column FROM $db_table";
		$where = $this->sqlWhere($where);
		if ($where) {
			$sql .= " WHERE $where";
		}
		if ($order) {
			$sql .= " ORDER BY $order";
		}
		$sql .= " LIMIT $limit";#  echo $sql;exit;
		return $this->logs($sql, $call ? : 'find') ? : self::$adapter->get($sql);
	}

	/**
	 * 刪除數據庫表圖層
	 */
	public function delete($where = null, $column = '*')
	{
		$db_table = $this->from();
		$sql = "DELETE FROM $db_table";
		$where = $this->sqlWhere($where);
		if ($where) {
			$sql .= " WHERE $where";
		}
		return $this->logs($sql, 'delete') ? : self::$adapter->query($sql);
	}
	
	public function get($where = null, $column = null, $order = null, $group = [], $join = null)
	{
		$column = $column ? : ($this->primary_key ? : '*');
		return $this->find($where, $column, $order, 1, 'get');
	}
	
	public function count($where = null)
	{
		$db_table = $this->from();
		$sql = "SELECT COUNT(0) AS num FROM $db_table";
		$where = $this->sqlWhere($where);
		if ($where) {
			$sql .= " WHERE $where";
		}
		$row = $this->logs($sql, 'count') ? : self::$adapter->find($sql);
		return $num = $row ? (is_object($row) ? $row->num : $row) : 0;
	}

	/**
	 * 计算页码总数
	 * @param  array   $where 查询
	 * @param  integer $limit 每页条数
	 * @param  boolean $total 是否返回总条数
	 * @return integer|array  返回页码总数
	 */
	public function pageCount($where = null, $limit = 10, $total = false)
	{
		$count = $this->count($where);
		$pageCount = ceil($count / $limit);
		if ($total) {
			return [$count, $pageCount];
		}
		return $pageCount;
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
		return $this->logs($sql, 'select') ? : self::$adapter->select($sql);
	}

	public function logs($sql, $type = null)
	{
		if (is_array($this->return)) {
			if (in_array($type, $this->return)) {
				$this->logs[] = $sql;
			}
		} elseif (is_string($this->return) && $type === $this->return) {
			return $sql;
		}
		return false;
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
	
	public function update($set = [], $where = null, $order = null, $limit = null, $call = null)
	{
		$db_table = $this->from();
		$sql = "UPDATE $db_table SET ";
		$sql .= $this->sqlSet($set);
		
		$condition = '';
		$whereSql = $this->sqlWhere($where);
		if ($whereSql) {
			# $whereSql = is_numeric($whereSql) ? "`$this->primary_key` = $whereSql" : $whereSql;
			$condition .= " WHERE $whereSql";
		}
		
		if ($order) {
			$condition .= " ORDER BY $order";
		}
		if (null !== $limit) {
			$condition .= " LIMIT $limit";
		}
		$sql .= $condition;
		$exec = $this->logs($sql, $call ? : 'update') ? : $this->exec($sql);
		/*
		if ('update.status' == $this->return) {
			if ($exec) {
				return $row = $this->get($where, '*', $order);
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
			$result []= $this->update($set, $where, $order, $limit, 'set');
		}
		return $result;
	}
	
	/**
	 * 比较数组的键值
	 * @param  array $arr    要比较的数组
	 * @param  array $other  对比数组
	 * @param  array $ignore 忽略键名
	 * @param  bool  $null   附上键或值为null的项
	 * @return array         返回值不同的键名
	 */
	public function array_diff_kv($arr = [], $other = [], $ignore = [], $null = false)
	{
		foreach ($ignore as $row) {
			unset($arr[$row]);
		}

		$diff = [];
		foreach ($arr as $key => $value) {
			if (array_key_exists($key, $other)) {
				if ($value != $val = $other[$key]) {
					$diff[$key] = [$value, $val];
				}
			} elseif($null) {
				$diff[$key] = null;
			}
		}
		return $diff;
	}

	/**
	 * 消息队列 - 放在字段里之后处理
	 * @param  mixed  $where      查询数据
	 * @param  array  $set        缓存数据
	 * @param  string $column     缓存字段列名
	 * @return boolean|integer    处理结果
	 */
	public function fieldMessageQueue($where, $set = [], $column = 'cache_set')
	{
		# $column[] = $this->primary_key;
		$row = $this->find($where, $column);
		if ($row) {
			$json = $row->$column;
			# $json = '';
			if ($json) {
				$obj = (array) json_decode($json);
				$set += $obj;
				# print_r([$set, $obj]);exit;
			}
			$json = json_encode($set);
			return $this->update([$column => $json], $where);
		}
		return false;
	}
	
	public function __call($name, $arguments)
	{
		return call_user_func_array(array(self::$adapter, $name), $arguments);
	}

	/**
	 * 清除数组的数字键名项
	 * @param 	array 	$arr 	要处理的数组
	 * @return 	array 			处理结果
	 */
	public function clearArrayByKey($arr)
	{
		foreach ($arr as $key => $value) {
			if (is_numeric($key)) {
				unset($arr[$key]);
			}
		}
		return $arr;
	}
}
