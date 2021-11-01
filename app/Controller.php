<?php

namespace app;

class Controller
{
    public $vars;

    public function __construct($vars)
    {
        $this->vars = $vars;
    }

    public function runAction($action)
    {
        return $this->$action();
    }

    public function getParam($key)
    {
        if (array_key_exists($key, $this->vars)) {
            return $this->vars[$key];
        }

        return null;
    }

    public function getPostBody()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return urldecode(file_get_contents('php://input'));
        }
        return '';
    }

    public function getPostBodyParam($param)
    {
        $body = $this->getPostBody();

        if (empty($body)) {
            return NULL;
        }

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