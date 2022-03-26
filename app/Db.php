<?php

namespace app;

use PDO;

/**
 * Обёртка для работы с БД (PDO).
 */
class Db
{
    /**
     * @internal Соединение с БД.
     * @var \PDO|null
     */
    private $_instance = null;

    /**
     * @param array $config Конфиг БД.
     * @throws \Exception Выбрасывается если файл БД sqlite не был найден.
     */
    public function __construct($config)
    {
        if ($config['driver'] === 'mysql') {
            /** @var string $dsn Имя источника данных или DSN, содержащее информацию, необходимую для подключения к базе данных. */
            $dsn = implode([
                $config['driver'], ':',
                'host=', $config['host'], ';',
                'dbname=', $config['dbname'],
            ]);
            $this->_instance = new PDO($dsn, $config['username'], $config['password']);
        } elseif ($config['driver'] === 'sqlite') {
            /** @var string Относительный путь к БД sqlite. */
            $pathToDb = $config['path'];
            if (file_exists($pathToDb)) {
                /** @var string $dsn Имя источника данных или DSN, содержащее информацию, необходимую для подключения к базе данных. */
                $dsn = implode([ $config['driver'], ':', $pathToDb ]);
                $this->_instance = new PDO($dsn);
            } else {
                /** @var string $absPath Абсолютный путь (полученный из указанного относительного пути) до файла БД, который не существует. */
                $absPath = $this->getRealPath($pathToDb);
                throw new \Exception("Не удалось подключиться к БД: файл не найден ($absPath)");
            }
        }
    }

    /**
     * Соединение с БД.
     * @return \PDO
     * @throws \Exception Если экземпляр соединения с БД не инициализирован.
     */
    public function getInstance()
    {
        if (is_null($this->_instance)) {
            throw new \Exception('Экземпляр соединения с БД не инициализирован.');
        }

        return $this->_instance;
    }

    /**
     * Закрыть соединение с БД.
     * @return void
     */
    public function closeInstance()
    {
        $this->_instance = null;
    }

    /**
     * Абсолютный путь до файла (работает и для несуществующего файла).
     * @internal
     * @param string $pathToDb Относительный путь.
     * Добавлено в связи с тем, что {@link https://www.php.net/manual/ru/function.realpath.php realpath()} не работает с несуществующими файлами.
     * @return string
     */
    private function getRealPath($pathToDb)
    {
        /** @var array $pathinfo Информация о path в виде ассоциативного массива. */
        $pathinfo = pathinfo($pathToDb);
        /** @var string $dirname Наименование директории. */
        $dirname = $pathinfo['dirname'];
        /** @var string $file Наименование файла (с расширением). */
        $file = $pathinfo['basename'];
        /** @var string $absPath Абсолютный путь до файла. */
        $absPath = realpath($dirname) . '\\' . $file;

        return $absPath;
    }
}