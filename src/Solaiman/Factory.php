<?php

namespace Solaiman;

class Factory{

	public static $_Factory = 'ElasticSearch|mysql|';

	protected $_factoryInstance;

	public function __construct($factory = ''){

			if($factory  == ''){
				$factory = Factory::$_Factory;
				$factoryArray = explode('|',$factory);
				$factory = reset($factoryArray);
			}
			$this->_factoryInstance = $factory;
	}

	public function getFactory(){
		$di = new \Zend\Di\Di();
		$factory = $di->get("Solaiman\Drivers\\".$this->_factoryInstance);
	}


	protected function connect(\Solaiman\Connections\)
}