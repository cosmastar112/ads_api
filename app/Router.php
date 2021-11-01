<?php

namespace app;

use FastRoute;

class Router
{
    private $_config;

    public function __construct($config)
    {
        $this->_config = $config;
    }

    public function getRouteInfo()
    {
        //метод
        $httpMethod = $_SERVER['REQUEST_METHOD'];
        //запрашиваемый маршрут (убрать SCRIPT_NAME из REQUEST_URI; останется часть, которая представляет маршрут)
        $uri = $this->getRequestedRoute();

        $dispatcher = $this->getDispatcher();

        return $dispatcher->dispatch($httpMethod, $uri);
    }

    private function getRequestedRoute()
    {
        //убрать SCRIPT_NAME из REQUEST_URI; останется часть, которая представляет маршрут
        return str_replace($_SERVER['SCRIPT_NAME'] /*что искать*/, '' /*на что заменить*/, $_SERVER['REQUEST_URI']  /*где искать*/);
    }

    private function getDispatcher()
    {
        $dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {
            foreach($this->_config as $configGroup => $configGroupElements) {
                $r->addGroup($configGroup, $this->createGroupCallback($configGroupElements));
            }
        });

        return $dispatcher;
    }

    function createGroupCallback($configGroupElements)
    {
        return function(FastRoute\RouteCollector $r) use ($configGroupElements) {
            foreach($configGroupElements as $groupElement) {
                $r->addRoute($groupElement[0], $groupElement[1], $groupElement[2]);
            }
        };
    }
}