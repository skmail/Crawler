<?php

namespace Solaiman;

use \Zend\Di\Di as Di;

class Crawler{

	protected $_website = 'http://www.blue.ps/';

	public function __construct(){
		$this->_scan();
	}

	protected function _scan(){
		$di = new Di();
		$this->_crawler = $di->get('Solaiman\DIContainer\Crawler');
		$this->_crawler->logger()->info('Start scanning');
		
		$connection = new \Solaiman\connections\Http\Http($this->_crawler);
		if(false !== $connection ){
			$urlContent = $connection->execute($this->_website);
			$outputs = $connection->output();
			if($this->_crawler->config()->getConfig('direct_archive') !== TRUE){
				$this->saveToArchive($outputs);

			}
		}
	}
	protected function save(){
		$factory = new Factory();
		$factory->getFactory();	
	}
	protected function saveToArchive($data){
		$file_name = md5($this->_website).'.json';
		$dir = $this->_crawler->config()->getConfig('archive_dir');
		if(!is_writable($dir)){
			$this->_crawler->logger()->err("Archives directory is not writable \"{$dir}\"");
		}	
		$fp = fopen(rtrim($dir,'/').'/'.$file_name, 'w');
		if(fwrite($fp, \Zend\Json\Json::encode($data))){
			$this->_crawler->logger()->info("Archive file has been created");
		}else{
			$this->_crawler->logger()->info("Archive file creation failed");
		}
		fclose($fp);
	}
}