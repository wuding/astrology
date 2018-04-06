<?php

namespace Plugin;

class Robot
{
	public function __construct()
	{
		
	}
	
	public function __call($name, $arguments)
	{
		return [$name, $arguments, __METHOD__, __FILE__, __LINE__];
	}
	
	public function downloadList()
	{
		return [__METHOD__, __FILE__, __LINE__];
	}
}
