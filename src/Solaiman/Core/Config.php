<?php
namespace Solaiman\Core;
class Config{
	
	protected $_config;
	protected $_applicationConfig;

	public function __construct(){

			$this->_config = new \Zend\Config\Config(require CONFIG_PATH.'/global.php');
			$this->_config = $this->_config->toArray();;
			$this->_applicationConfig = new \Zend\Config\Config(require CONFIG_PATH.'/applications.php');
			$this->_applicationConfig = $this->_applicationConfig->toArray();
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


	public function applicationConfig($application = '' ,$configKey = '')
	{
		if($application !== ''){
			if($configKey !== ''){
				return $this->_applicationConfig[$application][$configKey];
			}else{
				return $this->_applicationConfig[$application];
			}
		}else{
			return $this->_applicationConfig;
		}
	}


}

