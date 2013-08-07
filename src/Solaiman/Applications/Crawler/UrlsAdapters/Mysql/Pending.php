<?php 

namespace Solaiman\Applications\Crawler\urlsAdapters\Mysql;

class Pending{

	public $site_url;

    public function exchangeArray($data)
    {
        $this->site_url     = (isset($data['site_url'])) ? $data['site_url'] : null;
    }

}

