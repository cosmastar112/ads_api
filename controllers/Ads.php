<?php

namespace controllers;

use app\Controller;
require './../app/Controller.php';

class Ads extends Controller
{
    public function create()
    {
        //параметры запроса
        $text = $this->getPostBodyParam('text');
        $price = $this->getPostBodyParam('price');
        $limit = $this->getPostBodyParam('limit');
        $banner = $this->getPostBodyParam('banner');

        //валидация
        //ошибки валидации
    }

    public function update()
    {
    }

    public function relevant()
    {
    }
}