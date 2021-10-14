<?php

namespace controllers;

use app\Controller;
require_once './../app/Controller.php';
use models\Ad;
require_once './../models/Ad.php';
use models\rep\AdRep;
require_once './../models/rep/AdRep.php';

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

        $model = new Ad(null, $text, $price, $limit, $banner);
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

        //создать модель в хранилище
        $rep = new AdRep();
        if ($rep->save($model)) {
            return json_encode([
                'message' => 'OK',
                'code' => 200,
                'data' => [
                    'text' => $text,
                    'price' => $price,
                    'limit' => $limit,
                    'banner' => $banner,
                ]
            ]);
        } else {
            //TODO: ошибка операции
        }
    }

    public function update()
    {
        header('Content-Type: application/json');

        //параметры запроса
        $id = $this->request->getUpdateQueryString();
        $text = $this->getPostBodyParam('text');
        $price = $this->getPostBodyParam('price');
        $limit = $this->getPostBodyParam('limit');
        $banner = $this->getPostBodyParam('banner');

        $model = new Ad($id, $text, $price, $limit, $banner);
        // //валидация
        if (!$model->validate('update')) {
            //первая ошибка валидации
            $firstError = $model->getFirstError();
            $response = [
                'message' => $firstError,
                'code' => 400,
                'data' => [],
            ];

            return json_encode($response);
        }

        //обновить модель в хранилище
        $rep = new AdRep();
        if ($rep->save($model)) {
            return json_encode([
                'message' => 'OK',
                'code' => 200,
                'data' => [
                    'id' => $id,
                    'text' => $text,
                    'price' => $price,
                    'limit' => $limit,
                    'banner' => $banner,
                ]
            ]);
        } else {
            //TODO: ошибка операции
        }
    }

    public function relevant()
    {
        $rep = new AdRep();
        $model = $rep->get(1);

        var_dump($model);
    }
}