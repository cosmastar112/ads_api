<?php

namespace app;

class Request
{
    public $method;
    public $requestedRoute;
    public $routeController;
    public $routeAction;

    public function __construct()
    {
    }
}