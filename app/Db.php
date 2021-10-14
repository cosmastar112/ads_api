<?php

namespace app;

use PDO;

class Db
{
    private $_instance;

    public function __construct($config)
    {
        $dsn = implode([
            $config['driver'], ':',
            'host=', $config['host'], ';',
            'dbname=', $config['dbname'],
        ]);

        $this->_instance = new PDO($dsn, $config['username'], $config['password']);
    }

    public function getInstance()
    {
       return $this->_instance;
    }

    public function closeInstance()
    {
        $this->_instance = null;
    }
}