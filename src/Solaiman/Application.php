<?php

namespace Solaiman;

class Application{
	
	protected $siteUrl = null;

	public function __construct(){
		
	}

	public function run(){
		$di = new \Zend\Di\Di();
		$crawler = $di->get('Solaiman\Crawler');
	}
}