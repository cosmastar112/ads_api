<?php

namespace models\rep;

use app\IRepository;

/**
 * Репозиторий рекламы
 */
class AdRep implements IRepository
{
    /** @var \PDO Экземпляр БД */
    public $db;

    /**
     *
     * @param \PDO $db Экземпляр БД
     * @throws \Exception
     */
    public function __construct($db)
    {
        $this->db = $db;
        if (!is_object($this->db)) {
            throw new \Exception('Не задан объект подключения к БД');
        }
    }

    /**
     * {@inheritDoc}
     * @param \models\Ad $model Модель для сохранения/обновления.
     */
    public function save($model)
    {
        //если id записи не указан
        if (is_null($model->id)) {
            //создать запись
            /** @var \PDOStatement|false $st Подготовленный запрос к базе данных */
            $st = $this->db->prepare('INSERT INTO ad(`text`, price, `limit`, banner) VALUES(:text, :price, :limit, :banner)');
            /** @var true|false $result Результат выполнения запроса к базе данных */
            $result = $st->execute([':text' => $model->text, ':price' => $model->price, ':limit' => $model->limit, ':banner' => $model->banner]);
        } else {
            //обновить запись
            /** @var \PDOStatement|false $st Подготовленный запрос к базе данных */
            $st = $this->db->prepare('UPDATE ad SET `text` = :text, price = :price, `limit` = :limit, banner = :banner WHERE id = :id');
            /** @var true|false $result Результат выполнения запроса к базе данных */
            $result = $st->execute([':text' => $model->text, ':price' => $model->price, ':limit' => $model->limit, ':banner' => $model->banner, ':id' => $model->id]);
        }

        //если количество затронутых строк больше нуля, то считать операцию успешной
        if ($result > 0) {
            return true;
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function get($id)
    {
        /** @var \PDOStatement|false $st Подготовленный запрос к базе данных */
        $st = $this->db->prepare('SELECT * FROM ad WHERE id = :id');
        //Запустить подготовленный запрос на выполнение
        $st->execute([':id' => $id]);

        //Извлечение следующей строки из результирующего набора
        return $st->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * {@inheritDoc}
     */
    public function getRelevant()
    {
        //выбрать записи с максимальной ценой (загрузить только id)
        /** @var \PDOStatement|false $st Подготовленный запрос к базе данных */
        $st = $this->db->query('SELECT id FROM ad WHERE price = (SELECT MAX(price) FROM ad WHERE `limit` > 0)');
        /** @var array $rows Массив строк из набора результатов */
        $rows = $st->fetchAll(\PDO::FETCH_ASSOC);

        if ($rows) {
            /** @var array $ids Массив id записей */
            $ids = array_column($rows, 'id');
            /** @var int|string $id Иидентификатор случайного элемента */
            $id = $ids[array_rand($ids, 1) /*array_rand возвращает ключ*/];

            //уменьшить limit на 1 (т.к. баннер был показан)
            //TODO: нет уверенности, уменьшился ли счётчик показа
            $this->_decrementLimit($id);

            return $this->get($id);
        }

        return false;
    }

    /**
     * @internal Уменьшить счётчик показа на 1
     * @param int|string $id Идентификатор записи
     * @return true|false В случае успешного выполнения операции возвращается true, иначе false.
     */
    private function _decrementLimit($id)
    {
        //обновить запись
        /** @var \PDOStatement|false $st Подготовленный запрос к базе данных */
        $st = $this->db->prepare('UPDATE ad SET `limit` = `limit` - 1 WHERE id = :id');
        /** @var true|false $result Результат выполнения запроса к базе данных */
        $result = $st->execute([':id' => $id]);

        if ($result) {
            /** @var int $rowCount Количество затронутых строк */
            $rowCount = $st->rowCount();
            if ($rowCount > 0) {
                return true;
            }
        }

        return false;
    }
}