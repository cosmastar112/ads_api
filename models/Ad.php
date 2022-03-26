<?php

namespace models;

/**
 * Модель рекламы (объявления)
 */
class Ad
{
    /** @var null|int Идентификатор. Должен быть null при сохранении, но обязателен при обновлении. */
    public $id;

    /** @var string Описание. */
    public $text;

    /** @var int|string Цена объявления. */
    public $price;

    /** @var int|string Кол-во показов. */
    public $limit;

    /** @var string URL-адрес баннера (картинки). */
    public $banner;

    /** @var array Ошибки валидации. */
    public $errors = [];

    /**
     *
     * @param null|int $id
     * @param string $text
     * @param int|string $price
     * @param int|string $limit
     * @param string $banner
     */
    public function __construct($id, $text, $price, $limit, $banner)
    {
        $this->id = $id;
        $this->text = $text;
        $this->price = $price;
        $this->limit = $limit;
        $this->banner = $banner;
    }

    /**
     * Валидировать модель.
     * @param string $validationCase Сценарий валидации.
     * @return boolean Если валидация пройдена, возвращается true, иначе false.
     */
    public function validate($validationCase)
    {
        /** @var string $methodName Название метода, который нужно вызвать для валидации; совпадает со сценарием валидации. */
        $methodName = 'validate' . $validationCase;
        $this->$methodName();

        //есть ошибки валидации
        if (!empty($this->errors)) {
            return false;
        }

        return true;
    }

    /**
     * Валидация создания объявления.
     * В случае появления ошибки добавляет её в {@see \models\Ad::$errors}.
     *
     * @return void
     */
    public function validateCreate()
    {
        if (empty($this->text) || !is_string($this->text)) {
            array_push($this->errors, 'Invalid text');
        }

        if (empty($this->price) || !is_numeric($this->price)) {
            array_push($this->errors, 'Invalid price');
        }

        if (empty($this->limit) || !is_numeric($this->limit)) {
            array_push($this->errors, 'Invalid limit');
        }

        if (empty($this->banner) || !is_string($this->banner)) {
            array_push($this->errors, 'Invalid banner link');
        }
    }

    /**
     * Валидация обновления объявления.
     * В случае появления ошибки добавляет её в {@see \models\Ad::$errors}.
     *
     * @see \models\Ad::validateCreate()
     * @return void
     */
    public function validateUpdate()
    {
        if (empty($this->id)) {
            array_push($this->errors, 'Invalid id');
        }

        //остальная валидация повторяет то, что уже есть
        $this->validateCreate();
    }

    /**
     * Вернуть первую ошибку валидации.
     * @return null|string Текстовое представление ошибки (если есть).
     */
    public function getFirstError()
    {
        if (count($this->errors) > 0) {
            return $this->errors[0];
        }

        return NULL;
    }
}