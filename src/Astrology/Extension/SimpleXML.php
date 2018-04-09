<?php

namespace Astrology\Extension;

class SimpleXML
{
	public $element = null;
	
	public function __construct($data = null)
	{
		if ($data) {
			$this->init($data);
		}
	}
	
	public function init($data = null)
	{
		$this->element = new \SimpleXMLElement($data);
	}
}
