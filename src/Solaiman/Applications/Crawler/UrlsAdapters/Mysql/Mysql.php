<?php 

namespace Solaiman\Applications\Crawler\urlsAdapters\Mysql;

class Mysql implements \Solaiman\Applications\Crawler\IUrlsAdapters{



	public function getUrls(){

		$dbTable = new \Solaiman\Applications\Crawler\urlsAdapters\Mysql\Table();		

       	return $dbTable->fetchAll();
       	
	}


}

