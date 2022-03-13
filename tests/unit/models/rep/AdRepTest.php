<?php

namespace tests\unit\models\rep;

use models\rep\AdRep;
use models\Ad;

class AdRepTest extends \Codeception\Test\Unit
{
    protected function setUp(): void
    {
        $this->getDb()->query('DELETE FROM ad; ALTER TABLE ad AUTO_INCREMENT = 1;');
    }

    protected function tearDown(): void
    {
       $this->getDb()->query('DELETE FROM ad; ALTER TABLE ad AUTO_INCREMENT = 1;');
    }

    public function testSave()
    {
        $text = 'text value';
        $price = 100;
        $limit = 1000;
        $banner = 'banner value';
        $model = new Ad(null, $text, $price, $limit, $banner);

        $db = $this->getDb();
        $rep = new AdRep($db);
        $result = $rep->save($model);

        $this->assertEquals(true, $result);
        $this->getModule('Db')->seeInDatabase('ad', [
            'text' => $text,
            'price' => $price,
            'limit' => $limit,
            'banner' => $banner,
        ]);
    }

    public function testGet()
    {
        //в БД нет записи
        $id = 1;
        $db = $this->getDb();
        $rep = new AdRep($db);
        $result = $rep->get($id);
        $this->assertEquals(false, $result);

        //в БД есть запись
        $this->_setUpBeforeGet();
        $id = 1;
        $db = $this->getDb();
        $rep = new AdRep($db);
        $result = $rep->get($id);
        $this->assertIsArray($result);
    }

    public function testGetRelevant()
    {
        //в БД нет записей
        $db = $this->getDb();
        $rep = new AdRep($db);
        $result = $rep->getRelevant();
        $this->assertEquals(false, $result);

        //в БД есть записи
        $this->_setUpBeforeGetRelevant(100, 5);
        $this->_setUpBeforeGetRelevant(350, 5); //id=2
        $this->_setUpBeforeGetRelevant(200, 5);
        $db = $this->getDb();
        $rep = new AdRep($db);
        $result = $rep->getRelevant();
        $this->assertIsArray($result);
        $this->assertArrayHasKey('id', $result);
        $this->assertEquals(2, $result['id']); //запись с максимальной ценой
        $this->assertArrayHasKey('limit', $result);
        $this->assertEquals(4, $result['limit']); //после показа счетчик уменьшится на 1
    }

    public function testGetRelevantIfZeroLimit()
    {
        //в БД есть запись, но лимит показов израсходован
        $this->_setUpBeforeGetRelevant(100, 0);
        $db = $this->getDb();
        $rep = new AdRep($db);
        $result = $rep->getRelevant();
        $this->assertEquals(false, $result);
    }

    private function _setUpBeforeGet()
    {
        $text = 'text value';
        $price = 100;
        $limit = 1000;
        $banner = 'banner value';
        //создать запись
        $st = $this->getDb()->prepare('INSERT INTO ad(`text`, price, `limit`, banner) VALUES(:text, :price, :limit, :banner)');
        $st->execute([':text' => $text, ':price' => $price, ':limit' => $limit, ':banner' => $banner]);
    }

    private function _setUpBeforeGetRelevant($price, $limit)
    {
        $text = 'text value';
        $price = $price;
        $limit = $limit;
        $banner = 'banner value';
        //создать запись
        $st = $this->getDb()->prepare('INSERT INTO ad(`text`, price, `limit`, banner) VALUES(:text, :price, :limit, :banner)');
        $st->execute([':text' => $text, ':price' => $price, ':limit' => $limit, ':banner' => $banner]);
    }

    private function getDb()
    {
        $dbModule = $this->getModule('Db');

        return $dbModule->driver->getDbh();
    }
}