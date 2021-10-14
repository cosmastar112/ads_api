<?php

namespace app;

require_once 'Router.php';
require_once 'Db.php';

class Application
{
    private $_router;
    private $_db;
    private static $_app;

    public const HTTP_RESPONSE_STATUS_CODE_NOT_FOUND = 404;

    public function __construct($config = [])
    {
        $this->_router = new Router($config['routerConfig']);
        $this->_db = new Db($config['dbConfig']);
        //сохранить ссылку на объект
        self::$_app = $this;
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

        echo $this->callContollerAction($request);
        // var_dump($request);
    }

    //доступ к объекту "Приложение"
    public static function getApp()
    {
        return self::$_app;
    }

    public function getDb()
    {
        return $this->_db->getInstance();
    }

    public function closeDbConnections()
    {
        $this->_db->closeInstance();
    }

    private function callContollerAction($request)
    {
        $controllerClass = ucfirst($request->routeController); /*сделать первый символ строки прописной*/
        $controllerClassWithNamespace = '\controllers\\' . $controllerClass;
        //вызвать обработчик маршрута (метод контроллера)
        include './../controllers/' . $controllerClass . '.php';
        $controller = new $controllerClassWithNamespace($request);

        return $controller->runAction($request->routeAction);
    }
}