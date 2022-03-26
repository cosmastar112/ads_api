<?php

namespace app;

use FastRoute;

/**
 * Маршрутизатор.
 */
class Router
{
    /**
     * @internal
     * @var array Конфиг путей.
     */
    private $_config;

    /**
     * @param array $config {@link https://github.com/nikic/FastRoute#defining-routes Конфиг путей}.
     */
    public function __construct($config)
    {
        $this->_config = $config;
    }

    /**
     * Информация о маршруте.
     * @see \FastRoute\Dispatcher::dispatch()
     * @link https://github.com/nikic/FastRoute#dispatching-a-uri
     * @return array
     */
    public function getRouteInfo()
    {
        /** @var string $httpMethod Метод запроса. */
        $httpMethod = $_SERVER['REQUEST_METHOD'];
        /** @var $uri Запрашиваемый путь. */
        $uri = $this->getRequestedRoute();
        /** @var \FastRoute\Dispatcher Диспетчер. */
        $dispatcher = $this->getDispatcher();

        return $dispatcher->dispatch($httpMethod, $uri);
    }

    /**
     * Запрашиваемый путь.
     * @internal
     * @return string
     */
    private function getRequestedRoute()
    {
        //убрать SCRIPT_NAME из REQUEST_URI; останется часть, которая представляет маршрут
        return str_replace($_SERVER['SCRIPT_NAME'] /*что искать*/, '' /*на что заменить*/, $_SERVER['REQUEST_URI']  /*где искать*/);
    }

    /**
     * Диспетчер, определяющий информацию о маршруте по запрашиваемому адресу.
     * @internal
     * @return \FastRoute\Dispatcher
     */
    private function getDispatcher()
    {
        $dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {
            /**
             * {@link https://github.com/nikic/FastRoute#defining-routes}
             * @var string $configGroup Группа элементов.
             * @var array $configGroupElements Элементы группы, где каждый элемент
             * также является массивом, который состоит из: HTTP-метода, паттерна, контроллера/экшна.
             */
            foreach($this->_config as $configGroup => $configGroupElements) {
                $r->addGroup($configGroup, $this->createGroupCallback($configGroupElements));
            }
        });

        return $dispatcher;
    }

    /**
     * @internal
     * @param array $configGroupElements Элементы группы.
     * @see \app\Router::getDispatcher()
     * @return callable
     */
    private function createGroupCallback($configGroupElements)
    {
        return function(FastRoute\RouteCollector $r) use ($configGroupElements) {
            foreach($configGroupElements as $groupElement) {
                $r->addRoute($groupElement[0], $groupElement[1], $groupElement[2]);
            }
        };
    }
}