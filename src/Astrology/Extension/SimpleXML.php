<?php

namespace Astrology\Extension;

class SimpleXML
{
	public $_element = null;
	
	public function __construct($data = null)
	{
		if ($data) {
			return $this->init($data);
		}
	}
	
	public function init($data = null)
	{
		$xml = null;
		try {
			$xml = new \SimpleXMLElement($data);
		} catch (\Exception $e) {
			$xml = [$e->getMessage()];
		}
		return $this->_element = $xml;
	}
	
	public function getElement($data = null)
	{
		if (null !== $data) {
			$this->init($data);
		}
		return $this->_element;
	}
}
