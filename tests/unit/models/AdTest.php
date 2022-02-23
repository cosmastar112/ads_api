<?php
namespace models;

use models\Ad;

class AdTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _before()
    {
    }

    protected function _after()
    {
    }

    public function testValidateCreate()
    {
        //параметр запроса text обязательный
        $text = null;
        $model = new Ad(null, $text, null, null, null);
        $model->validate('create');
        $this->assertSame('Invalid text', $model->getFirstError());

        //параметр запроса text должен быть строкой
        $text = 1;
        $model = new Ad(null, $text, null, null, null);
        $model->validate('create');
        $this->assertSame('Invalid text', $model->getFirstError());


        //параметр запроса price обязательный
        $text = '1';
        $price = null;
        $model = new Ad(null, $text, $price, null, null);
        $model->validate('create');
        $this->assertSame('Invalid price', $model->getFirstError());

        //параметр запроса price должен быть числом или строкой, содержащей число
        $text = '1';
        $price = 'ss';
        $model = new Ad(null, $text, $price, null, null);
        $model->validate('create');
        $this->assertSame('Invalid price', $model->getFirstError());


        //параметр запроса limit обязательный
        $text = '1';
        $price = '1';
        $limit = null;
        $model = new Ad(null, $text, $price, $limit, null);
        $model->validate('create');
        $this->assertSame('Invalid limit', $model->getFirstError());

        //параметр запроса limit должен быть числом или строкой, содержащей число
        $text = '1';
        $price = '1';
        $limit = 's';
        $model = new Ad(null, $text, $price, $limit, null);
        $model->validate('create');
        $this->assertSame('Invalid limit', $model->getFirstError());


        //параметр запроса banner обязательный
        $text = '1';
        $price = '1';
        $limit = '1';
        $banner = null;
        $model = new Ad(null, $text, $price, $limit, $banner);
        $model->validate('create');
        $this->assertSame('Invalid banner link', $model->getFirstError());

        //параметр запроса banner должен строкой
        $text = '1';
        $price = '1';
        $limit = '1';
        $banner = 1;
        $model = new Ad(null, $text, $price, $limit, $banner);
        $model->validate('create');
        $this->assertSame('Invalid banner link', $model->getFirstError());
    }

    public function testValidateUpdate()
    {
        //параметр запроса id обязательный
        $id = null;
        $model = new Ad($id, null, null, null, null);
        $model->validate('update');
        $this->assertSame('Invalid id', $model->getFirstError());

        //параметр запроса text обязательный
        $id = 1;
        $text = null;
        $model = new Ad($id, $text, null, null, null);
        $model->validate('update');
        $this->assertSame('Invalid text', $model->getFirstError());

        //параметр запроса text должен быть строкой
        $text = 1;
        $model = new Ad($id, $text, null, null, null);
        $model->validate('update');
        $this->assertSame('Invalid text', $model->getFirstError());


        //параметр запроса price обязательный
        $text = '1';
        $price = null;
        $model = new Ad($id, $text, $price, null, null);
        $model->validate('update');
        $this->assertSame('Invalid price', $model->getFirstError());

        //параметр запроса price должен быть числом или строкой, содержащей число
        $text = '1';
        $price = 'ss';
        $model = new Ad($id, $text, $price, null, null);
        $model->validate('update');
        $this->assertSame('Invalid price', $model->getFirstError());


        //параметр запроса limit обязательный
        $text = '1';
        $price = '1';
        $limit = null;
        $model = new Ad($id, $text, $price, $limit, null);
        $model->validate('update');
        $this->assertSame('Invalid limit', $model->getFirstError());

        //параметр запроса limit должен быть числом или строкой, содержащей число
        $text = '1';
        $price = '1';
        $limit = 's';
        $model = new Ad($id, $text, $price, $limit, null);
        $model->validate('update');
        $this->assertSame('Invalid limit', $model->getFirstError());


        //параметр запроса banner обязательный
        $text = '1';
        $price = '1';
        $limit = '1';
        $banner = null;
        $model = new Ad($id, $text, $price, $limit, $banner);
        $model->validate('update');
        $this->assertSame('Invalid banner link', $model->getFirstError());

        //параметр запроса banner должен строкой
        $text = '1';
        $price = '1';
        $limit = '1';
        $banner = 1;
        $model = new Ad($id, $text, $price, $limit, $banner);
        $model->validate('update');
        $this->assertSame('Invalid banner link', $model->getFirstError());
    }
}