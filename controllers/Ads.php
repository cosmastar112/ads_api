<?php

namespace controllers;

use app\Controller;
use app\Application;
use models\Ad;
use models\rep\AdRep;

class Ads extends Controller
{
    public $db;

    public function getDb()
    {
        if (!is_object($this->db)) {
            $this->db = Application::getApp()->getDb();
        }
        return $this->db;
    }

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
        $rep = new AdRep($this->getDb());
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
        $id = $this->getParam('id');
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
        $rep = new AdRep($this->getDb());
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
        header('Content-Type: application/json');

        $rep = new AdRep($this->getDb());
        $model = $rep->getRelevant();
        if ($model) {
            return json_encode([
                'message' => 'OK',
                'code' => 200,
                'data' => [
                    'id' => $model['id'],
                    'text' => $model['text'],
                    'banner' => $model['banner'],
                ]
            ]);
        } else {
            //TODO: ошибка операции
        }
    }
}