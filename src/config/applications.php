<?php

$applications = array();

$applications['Crawler'] = array();
$applications['Crawler']['db'] = array(
        'driver'         => 'Pdo',
        'dsn'            => 'mysql:dbname=crawler;host=127.0.0.1',
        'driver_options' => array(
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
        ),
        'username' => 'root',
        'password' => ''
    );
return $applications;