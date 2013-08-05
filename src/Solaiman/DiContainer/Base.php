<?php

namespace Solaiman\DIContainer;

class Base {
	public function logger(){
			return new \Solaiman\Core\Logger($this->config());
	}

	public function config(){
		return new \Solaiman\Core\Config();
	}
}