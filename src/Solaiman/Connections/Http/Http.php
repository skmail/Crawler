<?php
namespace Solaiman\Connections\Http;
use Zend\Http\Client as HttpClient;

class Http{

	protected $_crawler;

	protected $_finishedUrls = array();
	
	protected $_outputs = array();

	protected $_maximum ;

	protected $_counter = 1;

	protected $_errorsNo = 0;

	protected $_timeout = 10;

	public function __construct(\Solaiman\DIContainer\Crawler $crawler)
	{
		$this->_crawler = $crawler;
	}

	public function execute($url){
		if($this->_getCounter() > $this->_getMaximum() && $this->_getMaximum() !== 0 ){
			return;
		}
		if(!$this->_filterUrl($url)){
			$this->_crawler->logger()->err("Url {$url} is not valid url");
			$this->_errorsNo++;
			return;
		}
		

		$options = array();
		$options['timeout'] = $this->_getTimeout();
		
		$client = new HttpClient();
        $client->setAdapter('Zend\Http\Client\Adapter\Curl');
        
        $client->setOptions($options);
        $response = $client->getResponse();
        $response->getHeaders()->addHeaderLine('content-type', 'text/html; charset=utf-8'); 
        $client->setUri($url);
        $this->counter++;
        $this->_crawler->logger()->info("Connection  " . $this->_getCounter() . ", Maximum connections: " . (($this->_getMaximum() == 0)?'Unlimited':$this->_getMaximum()));
        $this->_crawler->logger()->info("Connecting to \"{$url}\"");

        $result = $client->send();
		$this->_crawler->logger()->info('Fetching page contents');
		$body = $result->getBody();
		
		$vars = array();

		$this->_crawler->logger()->info('Start fetching title');
		$title = $this->_getTitle($body);
		if( $title !== false){
			$vars['title'] = $title;
			$this->_crawler->logger()->info('Page Title fetched succefully');
		}else{
			$this->_crawler->logger()->info('There is not title for this site');
		}
		$this->_crawler->logger()->info('Start fetching metas');
		$meta = $this->_getMeta($body);
		
		if(count($meta) > 0){
			$vars['meta'] = $meta;
			$this->_crawler->logger()->info('Metas fetched succefully');
		}else{
			$this->_crawler->logger()->info('There is not meta for this site');
		}
		$vars['url'] = $url;
		
		$this->_crawler->logger()->info('Start fetching images');
		$images = $this->_getImages($body);
		if(count($images) > 0){
			$vars['images'] = $images;
			$this->_crawler->logger()->info('Images fetched succefully');
		}

		$vars['time'] = time();
			
		$this->_finishedUrls[] = $url;
		
		$this->_outputs[] = $vars;		
		$this->_crawler->logger()->info("\"{$url}\" executed succefully");
		$this->_crawler->logger()->info('----------------------');
		$this->_plusCounter();
		if($this->_crawler->config()->getConfig('recursive_archive') == true){
			$urls = $this->_getUrls($body);
			if(count($url) > 0){
				foreach ($urls as $url) {
					if(!in_array($url,$this->_finishedUrls)){
						$this->execute($url);
					}
				}
			}
		}
	}

	protected function _getMeta($text){
		preg_match_all('/<meta(.*?)name=[\',"](.*?)[\',"](.*?)>/s', $text, $matches);
		$metas = array();
		$matches = reset($matches);
		if(is_array($matches)){
			foreach($matches as $matched){
				$pattern = "/%s=[',\"](.*?)[',\"]/";
				if(preg_match(sprintf($pattern,'name'), $matched, $metaName)){
					if(preg_match(sprintf($pattern,'content'), $matched, $metaContent)){
						$metas[$metaName[1]] = $metaContent[1];
					}
				}
			}
		}
		return $metas;
	}

	protected function _getTitle($text){
		preg_match('/<title>(.*?)<\/title>/s', $text, $match );
		if($match != ""){
			return $match[1];
		}
		return false;
	}

	protected function _getImages($text){
		preg_match_all('/<img(.*?)src=[\',"](.*?)[\',"](.*?)alt=[\',"](.*?)[\',"](.*?)>/s', $text, $matches);
		$images = array();

		foreach($matches[0] as $k=>$matched){
			$images[$k] = array();
			$pattern = "/%s=[',\"](.*?)[',\"]/";
			if(preg_match(sprintf($pattern,'src'), $matched, $src)){
				if(preg_match(sprintf($pattern,'alt'), $matched, $alt)){
					$images[$k]['src'] = $src[1];
					$images[$k]['alt'] = $alt[1];
				}
			}
		}
		return $images;
	}

	protected function _getUrls($text){
		preg_match_all('/<a(.*?)href=[\',"](.*?)[\',"](.*?)>(.*?)<\/a>/s', $text, $matches);
		$urls = array();
		foreach($matches[0] as $k=>$matched){
			$pattern = "/%s=[',\"](.*?)[',\"]/";
			if(preg_match(sprintf($pattern,'href'), $matched, $href)){
				if($this->_filterUrl($href['1'])){
					$urls[] = rtrim($href[1],'/');
				}
			}
		}
		return $urls;
	}

	protected function _filterUrl($url){
		return filter_var($url, FILTER_VALIDATE_URL);
	}

	public function output()
	{
		return $this->_outputs;
	}

	public function getErrorsNo(){
		return $this->_errorsNo;
	}

	public function release(){
		$this->_finishedUrls = array();	
		$this->_outputs = array();
		$this->counter = 1;
		$this->_errorsNo = 0;
	}

	protected function _getMaximum(){
		$this->_maximum = $this->_crawler->config()->getConfig('max_archive_urls');
		if(!is_numeric($this->_maximum)){
			$this->_maximum = 10;
		}
		return $this->_maximum;
	}

	protected function _getCounter(){
		return $this->_counter;
	}

	protected function _getTimeout(){
		return ($this->_crawler->config()->getConfig('max_archive_urls') != "")?$this->_crawler->config()->getConfig('max_archive_urls'):$this->_timeout;
	}

	protected function _plusCounter(){
		$this->_counter++;
	}
}