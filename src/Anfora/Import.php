<?php

namespace Anfora;

class Import
{
	public function __construct($file)
	{		
		return include_file($file);
	}
	
	public static function file($file)
	{
		return include_file($file);
	}
}

function include_file($file) {
	if (!isset($GLOBALS['ANFORA_IMPORT'])) {
		$GLOBALS['ANFORA_IMPORT'] = [];
	}
	$GLOBALS['ANFORA_IMPORT'][] = $file;
	return include $file;
}
