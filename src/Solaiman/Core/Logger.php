<?php
namespace Solaiman\Core;
class Logger extends \Zend\Log\Logger{
	protected $config;
	public function __construct(\Solaiman\Core\Config $config){
		parent::__construct();
		$this->config = $config;
		$writer = new \Zend\Log\Writer\Stream('php://stderr');
		$this->addWriter($writer);
	}
}
