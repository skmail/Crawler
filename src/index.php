<?php
	
	define('CONFIG_PATH');

	$di = new \Zend\Di\Di();
	
	$pplication = $di->get('\Solaiman\Application');
	
	$application->run();