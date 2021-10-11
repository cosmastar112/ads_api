<?php

namespace app;

require 'Router.php';

class Application
{
    private $_router;

    public function __construct($config = [])
    {
        $this->_router = new Router($config['routerConfig']);
    }

    public function run()
    {
        $request = $this->_router->parseRequest();
        //TODO: вызвать обработчик маршрута (метод контроллера)
        var_dump($request);
    }
}