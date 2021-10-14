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