<?php

namespace Astrology;

class Start
{
	public function __construct()
	{
		$GLOBALS['MODULE_NAME'] = '_Module';
		$controller = new \Controller;
	}
}
