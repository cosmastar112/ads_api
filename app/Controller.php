<?php

namespace app;

/**
 * Базовый класс, от которого должны наследоваться все контроллеры.
 */
class Controller
{
    /** @var array Параметры запроса */
    public $vars;

    /**
     *
     * @param array $vars
     */
    public function __construct($vars)
    {
        $this->vars = $vars;
    }

    /**
     * Вызвать указанный метод контроллера.
     * @param string $action Наименование метода (экшена), который нужно вызвать.
     * @return string
     */
    public function runAction($action)
    {
        return $this->$action();
    }

    /**
     * Параметр запроса.
     * @param string $key Наименование параметра.
     * @return string|int|null
     */
    public function getParam($key)
    {
        if (array_key_exists($key, $this->vars)) {
            return $this->vars[$key];
        }

        return null;
    }

    /**
     * Тело POST-запроса.
     * @return string Декодированная строка.
     */
    public function getPostBody()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return urldecode(file_get_contents('php://input'));
        }
        return '';
    }

    /**
     * Параметр тела POST-запроса.
     * @param string $param Наименование параметра.
     * @return string|null Декодированная строка
     */
    public function getPostBodyParam($param)
    {
        /** @var string Декодированная строка тела POST-запроса. */
        $body = $this->getPostBody();

        if (empty($body)) {
            return NULL;
        }

        /** @var array $parts Разбитая на части строка параметр=значение&параметр=значение на [параметр=значение, параметр=значение]. */
        $parts = explode('&', $body);
        /** @var string $part Подстрока "параметр=значение" */
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