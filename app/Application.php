<?php

namespace app;

require 'Router.php';

class Application
{
    private $_router;

    public const HTTP_RESPONSE_STATUS_CODE_NOT_FOUND = 404;

    public function __construct($config = [])
    {
        $this->_router = new Router($config['routerConfig']);
    }

    public function run()
    {
        $request = $this->_router->parseRequest();
        //Если реальный маршрут не удалось определить, вернуть 404-ю ошибку
        if (is_null($request->routeController)) {
            http_response_code(self::HTTP_RESPONSE_STATUS_CODE_NOT_FOUND);
            require('./../errors/404.html');
            die();
        }

        //TODO: вызвать обработчик маршрута (метод контроллера)
        var_dump($request);
    }
}