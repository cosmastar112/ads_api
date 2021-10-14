<?php

namespace models\rep;

use app\IRepository;
require_once './../app/IRepository.php';
use PDO;
use app\Application;

class AdRep implements IRepository
{
    public function save($model)
    {
        if (is_null($model->id)) {
            $st = Application::getApp()->getDb()->prepare('INSERT INTO ad(`text`, price, `limit`, banner) VALUES(:text, :price, :limit, :banner)');
            $result = $st->execute([':text' => $model->text, ':price' => $model->price, ':limit' => $model->limit, ':banner' => $model->banner]);
        }

        if ($result > 0) {
            return true;
        }

        return false;
    }

    public function get($id)
    {
        $st = Application::getApp()->getDb()->prepare('SELECT * FROM ad WHERE id = :id');
        $st->execute([':id' => $id]);

        return $st->fetch(PDO::FETCH_ASSOC);
    }

    public function getRelevant()
    {
    }
}