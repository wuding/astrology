<?php

namespace Controller;

use Astrology\Route;

class Index extends _Controller
{
	public function __construct()
	{
		parent::__construct();
	}

	public function __call($name, $arguments)
	{
		return [$name, $arguments, __FILE__, __LINE__];
	}

	public function index()
	{
		$tongji = $this->tongji;
		$location = 'urlnk.com' != ($_SERVER['HTTP_HOST'] ?? null) ? 'http://urlnk.com' : '';
		$redirect = $this->redirect;
		return get_defined_vars();
		include APP_PATH . '/_Module/View/Index/index.php';
		exit;
	}
}
