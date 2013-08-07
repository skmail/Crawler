<?php

namespace Solaiman;

class Application{
	
	protected $siteUrl = null;
	protected $_request;
	protected $_logger;

	public function __construct(\Solaiman\Core\Request $request,\Solaiman\Core\Logger $logger){
		$this->_logger = $logger;
		$this->_request = $request;
	}

	public function run(){
		$di = new \Zend\Di\Di();
		$applicationRoute = $this->_getApplicationRoute($this->_request->getParam('app-route'));
		if($applicationRoute){
			$applicationClass = "Solaiman\Applications\\{$applicationRoute['application']}\\{$applicationRoute['application']}";
			if(class_exists($applicationClass)){
				$applicationObject = $di->get($applicationClass);	
				if(method_exists($applicationObject, $applicationRoute['method'])){
					$applicationObject->$applicationRoute['method']();
				}else{
					$this->_logger->err("Method \"{$applicationRoute['method']}\" not found");	
				}
			}else{
				$this->_logger->err("Application \"{$applicationRoute['application']}\" not found");
			}
		}else{
			$this->_logger->err("Please run the application correctly i.e. <methodName>-<applicationName>");
		}
	}

	protected function _getApplicationRoute($applicationRoute = ''){
		$splitRoute = explode('-',$applicationRoute);
		if(count($splitRoute) !== 2){
			return false;
		}
		$router = array();
		$router['method'] = $splitRoute[0];
		$router['application'] = ucfirst($splitRoute[1]);
		return $router;
	}
}