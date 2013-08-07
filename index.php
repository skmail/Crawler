<?php

	include 'vendor/autoload.php';

	define('CONFIG_PATH', rtrim(dirname(__FILE__),'/').'/src/config');

	define('ARCHIVE_DIR', rtrim(dirname(__FILE__),'/').'/src/archives');

	$di = new \Zend\Di\Di();

	$application = $di->get('Solaiman\Application');

	$application->run();