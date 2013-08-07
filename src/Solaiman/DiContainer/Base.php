<?php

namespace Solaiman\DIContainer;
use \Zend\Di\Di as Di;
class Base {

	protected $DIContainer = NULL;

	public function __construct(){
		if($this->DIContainer == NULL )
		{
			$this->di = new Di();
		}
	}
	public function logger(){
			$parameters = array('config'=>$this->config());
			$c = $this->di->get('Solaiman\Core\Logger', $parameters);
			return $c;
	}

	public function config(){
		return new \Solaiman\Core\Config();
	}


	public function request(){
		$c = $this->di->get('Solaiman\Core\Request');
		return $c;	
	}
}