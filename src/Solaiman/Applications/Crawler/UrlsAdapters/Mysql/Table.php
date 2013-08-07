<?php


namespace Solaiman\Applications\Crawler\urlsAdapters\Mysql;

class Table extends \Zend\Db\TableGateway\AbstractTableGateway{
    
	protected $table = 'pendeing_sites';

    public function __construct()
    {	
	
    	$di =  new \Zend\Di\Di();
	
		$crawler = $di->get('Solaiman\DIContainer\Crawler');
		
        $this->adapter = new \Zend\Db\Adapter\Adapter($crawler->config()->applicationConfig('Crawler','db'));
        
        $resultSetPrototype = new  \Zend\Db\ResultSet\ResultSet();

        $resultSetPrototype->setArrayObjectPrototype(new \Solaiman\Applications\Crawler\urlsAdapters\Mysql\Pending());
		
		$this->resultSetPrototype = $resultSetPrototype;

        $this->initialize();
    }

    public function fetchAll()
    {	
        $resultSet = $this->select();
        return $resultSet;
    }
}
