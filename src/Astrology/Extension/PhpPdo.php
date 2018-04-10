<?php

namespace Astrology\Extension;

class PhpPdo
{
	public static $dns = null;
	public static $dbh = null;
	
	public function __construct($arg = [])
	{
		if ($arg) {
			$this->init($arg);
		}
	}
	
	public function init($arg = [])
	{
		$this->setVar($arg);
		$this->getDsn();
		$this->getDbh();
	}
	
	public function setVar($arg = [])
	{
		foreach ($arg as $key => $value) {
			$this->$key = $value;
		}
	}
	
	public function query($statement = null)
	{
		return self::$dbh->query($statement, 0);
	}
	
	public function sth($statement = null, $input_parameters = [])
	{
		$sth = self::$dbh->prepare($statement);
		$sth->execute($input_parameters);
		return $sth;
	}
	
	public function insert($sql = null, $input_parameters = [], $name = null)
	{
		$sth = $this->sth($sql, $input_parameters);
		return self::$dbh->lastInsertId($name);
	}
	
	public function find($sql = null, $input_parameters = [])
	{
		$sth = $this->sth($sql, $input_parameters);
		return $sth->fetchObject();
	}
}
