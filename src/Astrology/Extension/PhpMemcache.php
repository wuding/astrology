<?php

namespace Astrology\Extension;

class PhpMemcache
{
	public $memcache = null;
	public $memcache_connect = null;
	public $memcache_get_flags = null;
	
	public function __construct()
    {
        //echo __LINE__ .' '. __FILE__ . PHP_EOL;
		$this->memcache = new \Memcache;
    }
    
	public function connect($host = 'localhost', $port = 11211, $timeout = 1)
	{
		return $this->memcache_connect = @$this->memcache->connect($host, $port, $timeout);
	}
	
	public function set($key, $var = null, $flag = null, $expire = null)
	{
		//flag MEMCACHE_COMPRESSED
		return $this->memcache->set($key, $var, $flag, $expire);
	}
	
	public function get($key)
	{
		$flags = null;
		$get = $this->memcache->get($key, $this->memcache_get_flags);//print_r($flags);exit;
		//$this->memcache_get_flags = $flags;
		return $get;
	}
	
	public function check($key, $var = null, $flag = null, $expire = null)
	{
		$value = $this->get($key);
		if (!$value) {
			if (is_string($var)) {
				eval("\$func = $var;");
				$value = $func();
			} else {
				$value = $var;
			}
			$set = $this->set($key, $value, $flag, $expire);
		}
		return $value;
	}
	
	public function __destruct()
    {
    }
}
