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
        //если id записи не указан
        if (is_null($model->id)) {
            //создать запись
            $st = Application::getApp()->getDb()->prepare('INSERT INTO ad(`text`, price, `limit`, banner) VALUES(:text, :price, :limit, :banner)');
            $result = $st->execute([':text' => $model->text, ':price' => $model->price, ':limit' => $model->limit, ':banner' => $model->banner]);
        } else {
            //обновить запись
            $st = Application::getApp()->getDb()->prepare('UPDATE ad SET `text` = :text, price = :price, `limit` = :limit, banner = :banner WHERE id = :id');
            $result = $st->execute([':text' => $model->text, ':price' => $model->price, ':limit' => $model->limit, ':banner' => $model->banner, ':id' => $model->id]);
        }

        //если количество затронутых строк больше нуля, то считать операцию успешной
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
        //выбрать записи с максимальной ценой (загрузить только id)
        $st = Application::getApp()->getDb()->query('SELECT id FROM ad WHERE price = (SELECT MAX(price) FROM ad WHERE `limit` > 0)');
        $rows = $st->fetchAll(PDO::FETCH_ASSOC);

        if ($rows) {
            //извлечь id записей
            $ids = array_column($rows, 'id');
            //выбрать один элемент со случайным id
            $id = $ids[array_rand($ids, 1) /*array_rand возвращает ключ*/];

            //уменьшить limit на 1 (т.к. баннер был показан)
            $this->_decrementLimit($id);

            return $this->get($id);
        }

        return false;
    }

    private function _decrementLimit($id)
    {
        //обновить запись
        $st = Application::getApp()->getDb()->prepare('UPDATE ad SET `limit` = `limit` - 1 WHERE id = :id');
        $result = $st->execute([':id' => $id]);

        //если количество затронутых строк больше нуля, то считать операцию успешной
        if ($result > 0) {
            return true;
        }

        return false;
    }
}