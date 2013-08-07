<?php

namespace Solaiman\Applications\Crawler;

use \Zend\Di\Di as Di;

class Crawler{

	protected $_di;
	public function __construct(){
		$di = $this->_di =  new Di();
		$this->_crawler = $di->get('Solaiman\DIContainer\Crawler');
	}
	
	public function scanUrl(){
		$this->_startScanUrl($this->_crawler->request()->getParam(1));
	}

	public function autoScan(){
		$urlsAdapter = $this->_crawler->request()->getParam(1);
		$urlsAdapter = ucfirst($urlsAdapter);

		$adapterClass = "Solaiman\Applications\Crawler\urlsAdapters\\{$urlsAdapter}\\{$urlsAdapter}";
		
		if(class_exists($adapterClass)){
			$adapterObj = $this->_di->get($adapterClass);
			if($adapterObj instanceof \Solaiman\Applications\Crawler\IUrlsAdapters){
					$urlsList = $adapterObj->getUrls();
					if(is_object($urlsList)){
						foreach($urlsList as $url){
							$this->_startScanUrl($url->site_url);
						}
					}else{
						$this->_crawler->logger()->err("No results or method provided should return an object set");			
					}
			}else{
				$this->_crawler->logger()->err("URL Adapter \"{$urlsAdapter}\" should be an instance \Solaiman\Applications\Crawler\IUrlsAdatpers");
			}
		}else{
			$this->_crawler->logger()->err("URL Adapter \"{$urlsAdapter}\" not installed");
		}

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
			$this->_crawler->logger()->err("Archive file creation failed");
		}
		fclose($fp);
	}

	protected function _startScanUrl($url){
				
		$connection = new \Solaiman\connections\Http\Http($this->_crawler);
		if(false == $connection ){
			$this->_crawler->logger()->err("Cannot establish connection");
			return false;
		}
		$urlContent = $connection->execute($url);
		$outputs = $connection->output();
		if(count($outputs)  == 0 &&  $connection->getErrorsNo() > 0){
			$this->_crawler->logger()->err("An errors occured while scanning {$url}");
		}else{
			if($connection->getErrorsNo() > 0){
				$this->_crawler->logger()->notice("An errors occured while scanning inner URLs in {$url}, But scanning proccess passed");	
			}
			if($this->_crawler->config()->getConfig('direct_archive') !== TRUE){
				$this->_crawler->logger()->info("Archiving {$url} results");
				$this->saveToArchive($outputs);
			}
		}
		$connection->release();
	}
}