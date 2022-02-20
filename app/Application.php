<?php

namespace app;

use FastRoute;

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
        $routeInfo = $this->_router->getRouteInfo();

        switch ($routeInfo[0]) {
            case FastRoute\Dispatcher::NOT_FOUND:
                http_response_code(self::HTTP_RESPONSE_STATUS_CODE_NOT_FOUND);
                require './../errors/404.html';
                break;
            case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
                $allowedMethods = $routeInfo[1];
                // TODO: вывести список доступных методов
                http_response_code(405);
                require './../errors/405.html';
                break;
            case FastRoute\Dispatcher::FOUND:
                $handler = $routeInfo[1];
                $vars = $routeInfo[2];
                echo $this->callContollerAction($handler, $vars);
                break;
        }

        die();
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

    private function callContollerAction($handler, $vars)
    {
        //извлечь наименование контроллера и экшена
        [$controller, $action] = explode('/', $handler);
        //создать объект контроллера и вызвать метод (экшен)
        $controllerClass = ucfirst($controller); /*сделать первый символ строки прописной*/
        $controllerClassWithNamespace = '\controllers\\' . $controllerClass;
        //вызвать обработчик маршрута (метод контроллера)
        include './../controllers/' . $controllerClass . '.php';
        $controller = new $controllerClassWithNamespace($vars);

        return $controller->runAction($action);
    }
}