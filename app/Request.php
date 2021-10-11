<?php

namespace app;

class Request
{
    public $requestedRoute;
    public $routeController;
    public $routeAction;
    public $httpCode;

    public function __construct()
    {
    }
}