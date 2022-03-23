<?php

namespace controllers;

use app\Controller;
use app\Application;
use models\Ad;
use models\rep\AdRep;

/**
 * Контроллер рекламы (объявлений)
*/
class Ads extends Controller
{
    /**
     * @var \PDO Экземпляр БД
     * @link https://www.php.net/manual/ru/book.pdo.php
     */
    public $db;

    /**
     * @return \PDO Экземпляр БД.
     */
    public function getDb()
    {
        if (!is_object($this->db)) {
            $this->db = Application::getApp()->getDb();
        }
        return $this->db;
    }

    /**
     * Создать рекламу (объявление)
     * @return string
     */
    public function create()
    {
        header('Content-Type: application/json');

        /** @var string|null Описание. */
        $text = $this->getPostBodyParam('text');
        /** @var int|string|null Цена объявления. */
        $price = $this->getPostBodyParam('price');
        /** @var int|string|null Кол-во показов. */
        $limit = $this->getPostBodyParam('limit');
        /** @var string|null URL-адрес баннера (картинки). */
        $banner = $this->getPostBodyParam('banner');

        /** @var \models\Ad Модель. */
        $model = new Ad(null, $text, $price, $limit, $banner);
        //валидация
        if (!$model->validate('create')) {
            /** @var null|string Первая ошибка валидации */
            $firstError = $model->getFirstError();
            $response = [
                'message' => $firstError,
                'code' => 400,
                'data' => [],
            ];

            return json_encode($response);
        }

        /** @var \models\rep\AdRep Репозиторий рекламы. */
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

    /**
     * Изменить рекламу (объявление)
     * @return string
     */
    public function update()
    {
        header('Content-Type: application/json');

        /** @var int|null Идентификатор. */
        $id = $this->getParam('id');
        /** @var string|null Описание. */
        $text = $this->getPostBodyParam('text');
        /** @var int|string|null Цена объявления. */
        $price = $this->getPostBodyParam('price');
        /** @var int|string|null Кол-во показов. */
        $limit = $this->getPostBodyParam('limit');
        /** @var string|null URL-адрес баннера (картинки). */
        $banner = $this->getPostBodyParam('banner');

        /** @var \models\Ad Модель. */
        $model = new Ad($id, $text, $price, $limit, $banner);
        //валидация
        if (!$model->validate('update')) {
            /** @var null|string Первая ошибка валидации */
            $firstError = $model->getFirstError();
            $response = [
                'message' => $firstError,
                'code' => 400,
                'data' => [],
            ];

            return json_encode($response);
        }

        /** @var \models\rep\AdRep Репозиторий рекламы. */
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

    /**
     * Выбрать рекламу (объявление) для показа
     * @return string
     */
    public function relevant()
    {
        header('Content-Type: application/json');

        /** @var \models\rep\AdRep Репозиторий рекламы. */
        $rep = new AdRep($this->getDb());
        /** @var array|false Выбранная для показа модель рекламы. */
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