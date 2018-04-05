<?php

namespace Anfora;

class Import
{
	public function __construct($file)
	{
		if (!isset($GLOBALS['ANFORA_IMPORT'])) {
			$GLOBALS['ANFORA_IMPORT'] = [];
		}
		return include_file($file);
	}
}

function include_file($file) {
	$GLOBALS['ANFORA_IMPORT'][] = $file;
	return include $file;
}
