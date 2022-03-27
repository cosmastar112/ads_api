# Тестовое задание API Рекламы

## Инструкция по разворачиванию проекта
### PHP
Использовался PHP 7.2.25.

#### Расширения
* [fileinfo](https://www.php.net/manual/ru/book.fileinfo.php) - для работы модуля создания документации phpDocumentor

### СУБД
В качестве рабочей БД по умолчанию используется [MySQL](https://github.com/cosmastar112/ads_api/blob/master/config/db-mysql.php), а для тестов – [SQLite](https://github.com/cosmastar112/ads_api/blob/master/config/db-sqlite.php).

#### Рабочая БД (MySQL)

_Примечание 1: используется СУБД MySQL 8.0.18._

_Примечание 2: используются данные из конфига [config/db-mysql.php](https://github.com/cosmastar112/ads_api/blob/master/config/db-mysql.php)_

Выполнить шаги:
* Создать БД «ads»
~~~
CREATE SCHEMA `ads` ;
~~~
* Создать пользователя «ad-api» (в БД «ads»), выдать ему права на БД «ads»
~~~
CREATE USER 'ad-api'@'localhost' IDENTIFIED BY 'password';
GRANT ALL ON ads.* TO 'ad-api'@'localhost';
~~~
* Применить миграции для БД «ads»
~~~
php <Директория проекта>\scripts\migrate-prod
~~~
или
~~~
cd /d <Директория проекта>
scripts\migrate-prod.bat
подтвердить запуск миграций (WARNING библиотеки миграций)
~~~

#### Тестовая БД (SQLite)
_Примечание 1: используется СУБД SQLite 3.37.2 2022-01-06 13:25:41_

_Примечание 2: используется данные из конфига [config/db-sqlite.php](https://github.com/cosmastar112/ads_api/blob/master/config/db-sqlite.php)_

* Создать БД и применить миграции для «ads-test»
~~~
php <Директория проекта>\scripts\migrate-test
~~~
или
~~~
cd /d <Директория проекта>
scripts\migrate-test.bat
~~~
В результате в директории db появится файл БД ads-test.db. На существование файла БД [полагается модуль DB](https://github.com/cosmastar112/ads_api/blob/master/codeception.yml#L13) тестового фреймворка codeception.
Для интерактивного взаимодействия с БД можно использовать [cmd-утилиту](https://github.com/cosmastar112/ads_api/blob/master/db/bin/sqlite3.exe).

###	Миграции
Используется библиотека миграций doctrine/migrations, конфигурируемая с помощью файлов:
* [migrations.php](https://github.com/cosmastar112/ads_api/blob/master/config/migrations.php)
* [migrations-db.php](https://github.com/cosmastar112/ads_api/blob/master/config/migrations-db.php)
* [migrations-db-test.php](https://github.com/cosmastar112/ads_api/blob/master/config/migrations-db-test.php)

Миграции находятся в директории [migrations](https://github.com/cosmastar112/ads_api/tree/master/migrations).

### Документация

Для создания документации используется библиотека [phpDocumentor](https://docs.phpdoc.org/3.0/guide/getting-started/installing.html) v3.3.0. Запуск осуществляется с помощью [скрипта](https://github.com/cosmastar112/ads_api/tree/master/scripts/doc.bat). Результатом является HTML-документация в директории [docs/phpdoc](https://github.com/cosmastar112/ads_api/tree/master/docs/phpdoc).

#### Статический анализатор

Используется статический анализатор [Psalm](https://psalm.dev/). Для запуска предназначен [скрипт](https://github.com/cosmastar112/ads_api/blob/master/scripts/run-static-analysis-tool.bat).

## Описание проекта
API состоит из элементов:
* создание
* редактирование
* открутка

| Название элемента	| Маршрут	| Тип запроса	| Примечание |
| --- | --- | --- | --- |
| создание | /ads |	POST | |
| редактирование | /ads/\<number\> | POST | В строке запроса передается id редактируемого элемента |
| открутка | /ads/relevant | GET |

### Создание
Пример запроса
~~~
POST /ads HTTP/1.1
Host: localhost
Content-Type: application/x-www-form-urlencoded
...

text=Advertisement1&price=300&limit=1000&banner=https://linktoimage.png
~~~

Описание полей
| Поле | Описание |	Тип |
| --- | --- | --- |
| text | Заголовок объявления | Строковый |
| price |	Стоимость одного показа |	Числовой |
| limit	| Лимит показов	| Числовой |
| banner | Ссылка на картинку | Строковый |

Пример ответа
~~~
HTTP/1.1 200 OK
Content-Type: application/json
...

{
  "message": "OK",
  "code": 200,
  "data": {
     "id": 123,
     "text": "Advertisement1",
     "banner": "https://linktoimage.png"   
  }
}
~~~

Пример ответа с ошибкой валидации поля
~~~
HTTP/1.1 200 OK
Content-Type: application/json
...

{
  "message": "Invalid banner link",
  "code": 400,
  "data": {}
}
~~~

### Редактирование
Пример запроса
~~~
POST /ads/1234 HTTP/1.1
Host: localhost
Content-Type: application/x-www-form-urlencoded
...

text=Advertisement123&price=450&limit=1200&banner=https://linktoimage.png
~~~

Пример ответа
~~~
HTTP/1.1 200 OK
Content-Type: application/json
...

{
  "message": "OK",
  "code": 200,
  "data": {
    "id": 123,
    "text": "Advertisement123",
    "banner": "https://linktoimage.png"
  }
}
~~~

### Открутка
Возвращается id, text и banner объявления. Выбирается по следующим условиям:
* У него должна быть самая высокая цена
* Количество открученных показов этого объявления не должно превышать лимит показов (поле limit).

Пример запроса
~~~
GET /ads/relevant HTTP/1.1
Host: localhost
...

~~~
Пример ответа
~~~
HTTP/1.1 200 OK
Content-Type: application/json
...

{
  "message": "OK",
  "code": 200,
  "data": {
     "id": 123,
     "text": "Advertisement1",
     "banner": "https://linktoimage.png"   
  }
}
~~~

## Описание реализации

Входной [скрипт](https://github.com/cosmastar112/ads_api/blob/master/web/index.php) расположен в директории [web](https://github.com/cosmastar112/ads_api/tree/master/web).
В нём создаётся экземпляр [приложения](https://github.com/cosmastar112/ads_api/blob/master/app/Application.php), который управляет обработкой запроса. 
Для настройки приложения используются конфиг-файлы, расположенные в директории [config](https://github.com/cosmastar112/ads_api/tree/master/config):
* [router.php](https://github.com/cosmastar112/ads_api/blob/master/config/router.php) для [Роутера](https://github.com/cosmastar112/ads_api/blob/master/app/Router.php)
* [db-mysql.php](https://github.com/cosmastar112/ads_api/blob/master/config/db-mysql.php) (или [db-sqlite.php](https://github.com/cosmastar112/ads_api/blob/master/config/db-sqlite.php)) для объекта, который отвечает за [соединение с БД](https://github.com/cosmastar112/ads_api/blob/master/app/Db.php)

Для обработки запроса Приложение использует Роутер. Задача роутера – определить, существует ли реальный маршрут путём сопоставления запрашиваемого маршрута с доступными маршрутами, перечисленными в конфиге роутера. Для маршрутизации используется библиотека [FastRoute](https://github.com/nikic/FastRoute).

Если маршрут определить не удалось, будет возвращена [ошибка 404](https://github.com/cosmastar112/ads_api/blob/master/errors/404.html).
Если метод не поддерживается, будет возвращена [ошибка 405](https://github.com/cosmastar112/ads_api/blob/master/errors/405.html).
Если маршрут существует и указан верный HTTP-метод, создается объект контроллера и вызывается нужный метод (экшен).

Все контроллеры располагаются в директории [controllers](https://github.com/cosmastar112/ads_api/tree/master/controllers) и должны наследоваться от [базового класса](https://github.com/cosmastar112/ads_api/blob/master/app/Controller.php). Приложение возвращает результат работы метода объекта.

В экшене (создание, редактирование, открутка), в зависимости от элемента, контроллера осуществляется:
* доступ к параметрам запроса (если они есть)
* валидация параметров (если они есть)
* выполнение операции
*	возврат результата.

Для валидации используется [модель](https://github.com/cosmastar112/ads_api/blob/master/models/Ad.php), которая располагается в директории [models](https://github.com/cosmastar112/ads_api/tree/master/models).
В случае ошибки валидации будет возвращена ошибка 400.
Для выполнения операции используется [другой объект](https://github.com/cosmastar112/ads_api/blob/master/models/rep/AdRep.php), в котором инкапсулирована работа с БД (паттерн Репозиторий). Для этого объект реализует [интерфейс](https://github.com/cosmastar112/ads_api/blob/master/app/IRepository.php).
Для работы с БД используется объект [Db](https://github.com/cosmastar112/ads_api/blob/master/app/Db.php) – обёртка над [PDO](https://www.php.net/manual/en/book.pdo.php). Обёртка обеспечивает работу с СУБД MySQL и SQLite. Соединение с БД открывается один раз при создании Приложения; доступ к нему предоставляется с помощью метода getDb.
В свою очередь, для использования приложения предназначен статический метод Application::getApp(), который возвращает ссылку на экземпляр Приложения, которая сохраняется при его создании.

## Развитие (TODO)
* Использовать библиотеки для решения задачи (с помощью composer):
  *	ORM для работы с БД
  *	Привести код к стандарту PSR-12
* Написать тесты (как минимум, модульные)
*	Развернуть в контейнере (Docker)
*	Новый алгоритм открутки
