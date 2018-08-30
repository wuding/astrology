<?php

namespace Controller;

use Astrology\Route;

class Index extends _Controller
{
	public function __call($name, $arguments)
	{
		return [$name, $arguments, __FILE__, __LINE__];
	}
	
	public function index()
	{
		# echo setlocale(LC_ALL, 0);exit;
		
		$arr = [
			'url', 'start', 'task',
			'millisec' => 2000,
		];
		return $this->array_variable($arr);
	}
}
