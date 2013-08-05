<?php
namespace Solaiman\Connections\Http;
use Zend\Http\Client as HttpClient;

class Http{

	protected $_crawler;

	protected $_finishedUrls = array();
	
	protected $_outputs = array();

	protected $maximum ;

	protected $counter = 1;

	public function __construct(\Solaiman\DIContainer\Crawler $crawler)
	{
		$this->_crawler = $crawler;

		$this->maximum = $this->_crawler->config()->getConfig('max_archive_urls');

		if(!is_numeric($this->maximum)){
			$this->maximum = 10;
		}
	}

	public function execute($url){

		if($this->counter > $this->maximum){
			return;
		}
		$client = new HttpClient();
        $client->setAdapter('Zend\Http\Client\Adapter\Curl');
        $response = $client->getResponse();
        $response->getHeaders()->addHeaderLine('content-type', 'text/html; charset=utf-8'); 
        $client->setUri($url);
        $this->counter++;
        $this->_crawler->logger()->info("Counter " . $this->counter . ", Maximum: " . $this->maximum);
        $this->_crawler->logger()->info("Connecting to \"{$url}\"");
        $result = $client->send();
		$body = $result->getBody();
		
		$vars = array();
		
		$this->_crawler->logger()->info('Start fetching title');
		$title = $this->_getTitle($body);
		if( $title !== false){
			$vars['title'] = $title;
			$this->_crawler->logger()->info('Finish fetching title');
		}else{
			$this->_crawler->logger()->info('There is not title for this site');
		}
		$this->_crawler->logger()->info('Start fetching metas');
		$meta = $this->_getMeta($body);
		
		if(count($meta) > 0){
			$vars['meta'] = $meta;
			$this->_crawler->logger()->info('Finish fetching metas');
		}else{
			$this->_crawler->logger()->info('There is not meta for this site');
		}
		$vars['url'] = $url;

		$images = $this->_getImages($body);
		if(count($images) > 0){
			$vars['images'] = $images;
		}
		$this->_finishedUrls[] = $url;
		$this->_outputs[] = $vars;		
		
		if($this->_crawler->config()->getConfig('recursion_archive') == true){
			$urls = $this->_getUrls($body);
			foreach ($urls as $url) {
				if(!in_array($url,$this->_finishedUrls)){
					$this->execute($url);
				}
			}
		}
	

	}

	protected function _getMeta($text){
		preg_match_all('/<meta (.*?)name=[\',"](.*?)[\',"](.*?)>/', $text, $matches);
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
		//preg_match('/<title>(.*?)</title>/s', $text, $match);
		preg_match('/<title>(.*?)<\/title>/si', $text, $match );
		if($match != ""){
			return $match[1];
		}
		return false;
	}

	protected function _getImages($text){
		preg_match_all('/<img (.*?)src=[\',"](.*?)[\',"](.*?)alt=[\',"](.*?)[\',"](.*?)>/', $text, $matches);
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
		preg_match_all('/<a (.*?)href=[\',"](.*?)[\',"](.*?)>(.*?)<\/a>/', $text, $matches);
		$urls = array();
		foreach($matches[0] as $k=>$matched){
			$pattern = "/%s=[',\"](.*?)[',\"]/";
			if(preg_match(sprintf($pattern,'href'), $matched, $href)){
				if(filter_var($href[1], FILTER_VALIDATE_URL)){
					$urls[] = $href[1];
				}
			}
		}
		return $urls;
	}


	public function output()
	{
		return $this->_outputs;
	}
}