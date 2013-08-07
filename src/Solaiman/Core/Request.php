<?php

namespace Solaiman\Core;

class Request extends \Zend\Console\Request
{
	public function __construct(){
        parent::__construct();
		$this->setParam('app-route',0);
	}
	public function setParam($name,$key){
		$paramsArray = $this->getParams()->toArray();
        $paramsArray[$name] = $paramsArray[$key];
        $parameters = new \Zend\Stdlib\Parameters($paramsArray);
        $this->setParams($parameters);
	}	
}