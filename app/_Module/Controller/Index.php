<?php

namespace Controller;

use Astrology\Route;

class Index extends _Controller
{
	public function __construct()
	{
		# parent::__construct();
		# print_r([__METHOD__, __FILE__, __LINE__]);
	}
	
	public function __call($name, $arguments)
	{
		return [$name, $arguments];
	}
}
