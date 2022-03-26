<?php

namespace app;

use FastRoute;

/**
 * Приложение.
 */
class Application
{
    /**
     * @internal
     * @var \app\Router Маршрутизатор.
     */
    private $_router;

    /**
     * @internal
     * @var \app\Db Обёртка для работы с БД.
     */
    private $_db;

    /**
     * @internal
     * @var \app\Application Экземпляр приложения.
     */
    private static $_app;

    /** @var int HTTP-код ответа "Страница не найдена". */
    public const HTTP_RESPONSE_STATUS_CODE_NOT_FOUND = 404;

    /**
     * @param array $config Конфиг приложения. Должен включать конфиги путей (маршруты) и БД.
     */
    public function __construct($config = [])
    {
        $this->_router = new Router($config['routerConfig']);
        $this->_db = new Db($config['dbConfig']);
        //ссылка на объект Приложения
        self::$_app = $this;
    }

    /**
     * Обработать запрос.
     * @return string
     */
    public function run()
    {
        /** @var array $routeInfo Информация о маршруте. */
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
                /** @var array $handler Маршрут (контроллер/экшен). */
                $handler = $routeInfo[1];
                /** @var array $vars Параметры запроса. */
                $vars = $routeInfo[2];
                echo $this->callContollerAction($handler, $vars);
                break;
        }

        die();
    }

    /**
     * Экземпляр приложения.
     * @return \app\Application
     */
    public static function getApp()
    {
        return self::$_app;
    }

    /**
     * Обёртка для работы с БД.
     * @return \PDO
     */
    public function getDb()
    {
        return $this->_db->getInstance();
    }

    /**
     * Закрыть соединение с БД.
     * @return void
     */
    public function closeDbConnections()
    {
        $this->_db->closeInstance();
    }

    /**
     * Запустить экшен контроллера.
     * @internal
     * @param array $handler Маршрут (контроллер/экшен).
     * @param array $vars Параметры запроса.
     * @return string
     */
    private function callContollerAction($handler, $vars)
    {
        /**
         * @var string $controller Наименование контроллера.
         * @var string $action Наименование экшена.
         */
        [$controller, $action] = explode('/', $handler);
        //создать объект контроллера и вызвать метод (экшен)
        /** @var string $controllerClass Наименование класса контроллера. */
        $controllerClass = ucfirst($controller); /*сделать первый символ строки прописной*/
        /** @var string $controllerClassWithNamespace Наименование класса контроллера, включая пространство имён. */
        $controllerClassWithNamespace = '\controllers\\' . $controllerClass;
        //вызвать обработчик маршрута (метод контроллера)
        include './../controllers/' . $controllerClass . '.php';

        /** @var \app\Controller $controller Объект указанного класса контроллера. */
        $controller = new $controllerClassWithNamespace($vars);

        return $controller->runAction($action);
    }
}