<?php
namespace Solaiman\Core;
class Config{
	
	protected $_config;

	public function __construct(){

			$this->_config = new \Zend\Config\Config(require CONFIG_PATH.'/global.php');

			date_default_timezone_set($this->getConfig('date_default_timezone'));
	}


	public function getConfig($configKey = '')
	{
		if($configKey !== ''){
			return $this->_config[$configKey];
		}else{
			return $this->_config;
		}
	}
}

