<?php

namespace app;

class Request
{
    public $method;
    public $requestedRoute;
    public $routeController;
    public $routeAction;

    public function getUpdateQueryString()
    {
        //убрать наименование контроллера
        $queryString = str_replace($this->routeController, '', $this->requestedRoute);
        //убрать слеши
        $queryString = str_replace('/', '', $queryString);

        return $queryString;
    }
}