<?php

namespace Astrology\Extension;

use PDO;
use Exception;
use PDOException;

class PhpPdoMysql extends PhpPdo
{
	public function __construct($arg = [])
	{
		parent::__construct($arg);
	}
	
	public function getDsn($host = null, $port = null, $db_name = null)
	{
		$host = $host ? : $this->host;
		$port = $port ? : $this->port;
		$db_name = $db_name ? : $this->db_name;
		return self::$dns = "mysql:host=$host;port=$port;dbname=$db_name";
	}
	
	public function getDbh($username = null, $password = null)
	{
		$username = $username ? : $this->username;
		$password = $password ? : $this->password;
		try {
			self::$dbh = new PDO(self::$dns, $username, $password);
		} catch (PDOException $e) {
			print_r([$e->getMessage(), __FILE__, __LINE__]);
			exit;
		}
		return self::$dbh;
	}
}
