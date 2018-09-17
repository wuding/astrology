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
	
	public function download($http_header = null, $header = null)
	{
		$ch = $this->ch;
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		if ($http_header) {
			curl_setopt($ch, CURLOPT_HTTPHEADER, $http_header);
		}
		
		if ($header) {
			curl_setopt($ch, CURLOPT_HEADER, TRUE);    //表示需要response header
			curl_setopt($ch, CURLOPT_NOBODY, FALSE); //表示需要response body
		}
		
		
		$response = $this->_exec($ch, 0);
		
		if ($header) {
			$code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
			$headerSize = (int) curl_getinfo($ch, CURLINFO_HEADER_SIZE);
			if (500 > $code) {
				$header = substr($response, 0, $headerSize);
				$body = substr($response, $headerSize);
				$response = [trim($header), $body];
				# print_r($response);
				# exit;
			} else {
				print_r($response);
				exit;
			}
		}
		curl_close($ch);
		return $response;
	}
	
	public function simulate($data = null, $http_header = null, $header = null)
	{
		$ch = $this->ch;
		/*
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $your_url);
		*/
		$user_agent = 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/57.0.2987.133 Safari/537.36';
		curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		if ($http_header) {
			curl_setopt($ch, CURLOPT_HTTPHEADER, $http_header);
		}
		
		if (null !== $data) {
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		}
		# curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		
		if ($header) {
			curl_setopt($ch, CURLOPT_HEADER, TRUE);    //表示需要response header
			curl_setopt($ch, CURLOPT_NOBODY, FALSE); //表示需要response body
		}
		
		$response = $this->_exec($ch, 0);
		if ($header) {
			$code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
			$headerSize = (int) curl_getinfo($ch, CURLINFO_HEADER_SIZE);
			if (500 > $code) {
				$header = substr($response, 0, $headerSize);
				$body = substr($response, $headerSize);
				$response = [trim($header), $body];
				# print_r($response);
				# exit;
			} else {
				print_r($response);
				exit;
			}
		}
		curl_close($ch);
		return $response;
	}
	
	public function _exec($ch, $close = 1)
	{
		$exec = curl_exec($ch);//执行预定义的CURL 
		$this->errno = curl_errno($ch);
		if(!$this->errno){ 
		  $this->info = curl_getinfo($ch);
		} else { 
		  $this->error = curl_error($ch); 
		}
		
		if ($close) {
			curl_close($ch);
		}
		return $exec;
	}
	
	public function __destruct()
    {
    }
}
