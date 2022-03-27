<?php

namespace models;

/**
 * Модель рекламы (объявления)
 */
class Ad
{
    /** @var int|string|null Идентификатор. Должен быть null при сохранении, но обязателен при обновлении. */
    public $id;

    /** @var string|null Описание. */
    public $text;

    /** @var int|string|null Цена объявления. */
    public $price;

    /** @var int|string|null Кол-во показов. */
    public $limit;

    /** @var string|null URL-адрес баннера (картинки). */
    public $banner;

    /** @var array Ошибки валидации. */
    public $errors = [];

    /**
     *
     * @param int|string|null $id
     * @param string|null $text
     * @param int|string|null $price
     * @param int|string|null $limit
     * @param string|null $banner
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
        if (!is_string($this->text) || $this->text === '') {
            array_push($this->errors, 'Invalid text');
        }

        if (!is_numeric($this->price)) {
            array_push($this->errors, 'Invalid price');
        }

        if (!is_numeric($this->limit)) {
            array_push($this->errors, 'Invalid limit');
        }

        if (!is_string($this->banner) || $this->banner === '') {
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