<?php

namespace controllers;

class Ads
{
    public $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    private function allowedMethods()
    {
        //экшн => разрешенные методы (HTTP)
        return [
            'create' => ['POST'],
            'update' => ['POST'],
            'relevant' => ['GET'],
        ];
    }

    public function create()
    {
        if(!$this->filterMethod(__FUNCTION__)) {
            http_response_code(405);
            require('./../errors/405.html');
            die();
        }
    }

    private function filterMethod($func)
    {
        $allowedMethods = $this->allowedMethods();
        if (isset($allowedMethods[$func])) {
            if (in_array($this->request->method, $allowedMethods[$func])) {
                return true;
            }
        }

        return false;
    }

    public function update()
    {
    }

    public function relevant()
    {
    }
}