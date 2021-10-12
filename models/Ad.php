<?php

namespace models;

class Ad
{
    public $id;
    public $text;
    public $price;
    public $limit;
    public $banner;
    public $errors = [];

    public function __construct($id, $text, $price, $limit, $banner)
    {
        $this->id = $id;
        $this->text = $text;
        $this->price = $price;
        $this->limit = $limit;
        $this->banner = $banner;
    }

    public function validate($validationCase)
    {
        $methodName = 'validate' . $validationCase;
        $this->$methodName();

        //есть ошибки валидации
        if (!empty($this->errors)) {
            return false;
        }

        return true;
    }

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

    public function validateUpdate()
    {
        if (empty($this->id)) {
            array_push($this->id, 'Invalid id');
        }

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

    public function getFirstError()
    {
        if (count($this->errors) > 0) {
            return $this->errors[0];
        }

        return NULL;
    }
}