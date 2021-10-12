<?php

namespace app;

class Controller
{
    public $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    protected function allowedMethods()
    {
        //экшн => разрешенные методы (HTTP)
        return [
            'create' => ['POST'],
            'update' => ['POST'],
            'relevant' => ['GET'],
        ];
    }

    protected function filterMethod($func)
    {
        $allowedMethods = $this->allowedMethods();
        if (isset($allowedMethods[$func])) {
            if (in_array($this->request->method, $allowedMethods[$func])) {
                return true;
            }
        } else {
            //для экшена не установлены правила; следовательно, пропускать все запросы
            return true;
        }

        return false;
    }

    public function runAction($action)
    {
        //фильтрация HTTP-метода
        if(!$this->filterMethod($action)) {
            http_response_code(405);
            require('./../errors/405.html');
            die();
        }

        return $this->$action();
    }
}