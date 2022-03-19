<?php

namespace app;

use PDO;

class Db
{
    private $_instance;

    public function __construct($config)
    {
        if ($config['driver'] === 'mysql') {
            $dsn = implode([
                $config['driver'], ':',
                'host=', $config['host'], ';',
                'dbname=', $config['dbname'],
            ]);
            $this->_instance = new PDO($dsn, $config['username'], $config['password']);
        } elseif ($config['driver'] === 'sqlite') {
            //относительный путь к БД
            $pathToDb = $config['path'];
            if (file_exists($pathToDb)) {
                $dsn = implode([ $config['driver'], ':', $pathToDb ]);
                $this->_instance = new PDO($dsn);
            } else {
                $absPath = $this->getRealPath($pathToDb);
                throw new \Exception("Не удалось подключиться к БД: файл не найден ($absPath)");
            }
        }
    }

    public function getInstance()
    {
       return $this->_instance;
    }

    public function closeInstance()
    {
        $this->_instance = null;
    }

    //абсолютный путь до несуществующего файла
    //сделано, т.к. realpath не работает с несуществующими файлами
    private function getRealPath($pathToDb)
    {
        $pathinfo = pathinfo($pathToDb);
        $dirname = $pathinfo['dirname'];
        $file = $pathinfo['basename'];
        $absPath = realpath($dirname) . '\\' . $file;

        return $absPath;
    }
}