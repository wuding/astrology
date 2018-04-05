<?php

namespace Controller;

class Robot extends _Controller
{
	public function __construct()
	{
		parent::__construct();
		print_r([__METHOD__, __FILE__, __LINE__]);
	}
}
