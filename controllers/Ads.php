<?php

namespace controllers;

use app\Controller;
require './../app/Controller.php';
use models\Ad;
require './../models/Ad.php';

class Ads extends Controller
{
    public function create()
    {
        header('Content-Type: application/json');

        //параметры запроса
        $text = $this->getPostBodyParam('text');
        $price = $this->getPostBodyParam('price');
        $limit = $this->getPostBodyParam('limit');
        $banner = $this->getPostBodyParam('banner');

        $model = new Ad($text, $price, $limit, $banner);
        //валидация
        if (!$model->validate('create')) {
            //первая ошибка валидации
            $firstError = $model->getFirstError();
            $response = [
                'message' => $firstError,
                'code' => 400,
                'data' => [],
            ];

            return json_encode($response);
        }

        //TODO: создать модель в хранилище
        // $id
        //ответ если все ОК
        // return [
        //     'message' => 'OK',
        //     'code' => 200,
        //     'data' => [
        //         'id' => $id,
        //         'text' => $text,
        //         'banner' => $banner,
        //     ]
        // ];
    }

    public function update()
    {
    }

    public function relevant()
    {
    }
}