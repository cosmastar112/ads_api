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

    public function getPostBody()
    {
        if ($this->request->method === 'POST') {
            return urldecode(file_get_contents('php://input'));
        }
        return '';
    }

    public function getPostBodyParam($param)
    {
        $body = $this->getPostBody();

        //разбить на части строку параметр=значение&параметр=значение на [параметр=значение, параметр=значение]
        $parts = explode('&', $body);
        foreach ($parts as $part) {
            //разбить на части строку параметр=значение на [параметр, значение]
            [$paramKey, $paramValue] = explode('=', $part);
            if ($paramKey === $param) {
                return $paramValue;
            }
        }

        return NULL;
    }
}