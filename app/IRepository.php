<?php

namespace app;

interface IRepository
{
    public function save($model);
    public function get($id);
    public function getRelevant();
}