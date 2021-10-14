<?php

namespace app;

require_once 'Request.php';

class Router
{
    private $_map;

    public function __construct($config)
    {
        $this->_map = $config;
    }

    public function parseRequest()
    {
        //Запрос
        $request = new Request();

        //метод
        $request->method = $_SERVER['REQUEST_METHOD'];
        //Поиск подстроки, представляющей маршрут
        $request->requestedRoute = $this->getRequestedRoute();

        //Поиск реального маршрута
        [$request->routeController, $request->routeAction] = $this->navigate($request->requestedRoute);

        return $request;
    }

    private function getRequestedRoute()
    {
        //убрать SCRIPT_NAME из REQUEST_URI; останется часть, которая представляет маршрут
        return str_replace($_SERVER['SCRIPT_NAME'] /*что искать*/, '' /*на что заменить*/, $_SERVER['REQUEST_URI']  /*где искать*/);
    }

    private function navigate($requestedRoute)
    {
        for($i = 0; $i < count($this->_map); $i++) {
            $mapItem = $this->_map[$i];
            $routePattern = $mapItem['p'];
            $realRoute = $mapItem['r'];
            if (preg_match($routePattern, $requestedRoute) === 1) {
                //шаблон подошёл
                return explode('/', $realRoute);
            }
        }

        //не удалось определить маршрут
        return [null, null];
    }
}