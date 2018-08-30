<?php
namespace Astrology\Extension;

class PhpCurl
{
	public $ch = null;
	public $info = null;
	public $error = null;
	public $errno = null;
	
	public function __construct($url = null)
    {
        $this->init($url);
    }
	
	public function init($url = null)
	{
		if ($url) {
			$this->ch = $ch = curl_init($url);
			# curl_setopt($ch, CURLOPT_URL, $url); //设置请求的URL
		}
	}
    
	public function request($data, $method = 'post')
	{
		$method = strtoupper($method);
		$ch = $this->ch;
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //设为TRUE把curl_exec()结果转化为字串，而不是直接输出 
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method); //设置请求方式
		 
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("X-HTTP-Method-Override: $method"));//设置HTTP头信息
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);//设置提交的字符串
		
		
		$exec = curl_exec($ch);//执行预定义的CURL 
		$this->errno = curl_errno($ch);
		if(!$this->errno){ 
		  $this->info = curl_getinfo($ch);
		} else { 
		  $this->error = curl_error($ch); 
		}
		curl_close($ch);
		
		return $exec;
	}
	
	public function download($http_header = null)
	{
		$ch = $this->ch;
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		if ($http_header) {
			curl_setopt($ch, CURLOPT_HTTPHEADER, $http_header);
		}
		
		return $this->_exec($ch);
	}
	
	public function _exec($ch)
	{
		$exec = curl_exec($ch);//执行预定义的CURL 
		$this->errno = curl_errno($ch);
		if(!$this->errno){ 
		  $this->info = curl_getinfo($ch);
		} else { 
		  $this->error = curl_error($ch); 
		}
		curl_close($ch);
		
		return $exec;
	}
	
	public function __destruct()
    {
    }
}
