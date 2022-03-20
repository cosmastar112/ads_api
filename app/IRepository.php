<?php

namespace app;

/**
 * IRepository это интерфейс, который должен быть реализован классами, в которых инкапсулируется работа с БД (паттерн Репозиторий).
 *
 * @link https://martinfowler.com/eaaCatalog/repository.html Паттерн Репозиторий
 */
interface IRepository
{
    /**
     * Сохранить/обновить запись.
     * @param mixed $model Модель для сохранения/обновления.
     * @return true|false В случае успешного выполнения операции возвращается true, иначе false.
     */
    public function save($model);

    /**
     * Найти запись по указанному id.
     * @param int|string $id Идентификатор записи
     * @return array|false В случае успешного выполнения операции возвращается массив, иначе false.
     */
    public function get($id);

    /**
     * Найти запись для показа.
     *
     * В методе реализуется логика выбора записи для показа.
     * Метод должен уменьшать счётчик показов выбранной записи на 1.
     *
     * @return array|false В случае успешного выполнения операции возвращается массив, иначе false.
     */
    public function getRelevant();
}