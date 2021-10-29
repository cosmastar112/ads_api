# Тестовое задание API Рекламы

## Инструкция по разворачиванию проекта
### PHP
Использовался PHP 7.2.25.
###	СУБД
Использовалась СУБД MySQL 8.0.18.
###	БД
Используется библиотека миграций doctrine/migrations, конфигурируемая с помощью файлов:
* [migrations.php](https://github.com/cosmastar112/ads_api/blob/master/config/migrations.php)
* [migrations-db.php](https://github.com/cosmastar112/ads_api/blob/master/config/migrations-db.php)

Миграции находятся в директории [migrations](https://github.com/cosmastar112/ads_api/tree/master/migrations).

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
* [db.php](https://github.com/cosmastar112/ads_api/blob/master/config/db.php) для объекта, который отвечает за [соединение с БД](https://github.com/cosmastar112/ads_api/blob/master/app/Db.php)

Для обработки запроса Приложение использует Роутер. Задача роутера – определить, существует ли реальный маршрут путём сопоставления запрашиваемого маршрута с доступными маршрутами, перечисленными в конфиге роутера. 
Если маршрут определить не удалось, будет возвращена [ошибка 404](https://github.com/cosmastar112/ads_api/blob/master/errors/404.html). Если маршрут существует, то определяется пара «контроллер/экшен», где контроллер – имя объекта, а экшен – его метод, который отвечает за обработку маршрута. 
Результат работы роутера – объект [Запрос](https://github.com/cosmastar112/ads_api/blob/master/app/Request.php) со свойствами «контроллер» и «экшен».

Все контроллеры располагаются в директории [controllers](https://github.com/cosmastar112/ads_api/tree/master/controllers) и должны наследоваться от [базового класса](https://github.com/cosmastar112/ads_api/blob/master/app/Controller.php). 
Приложение возвращает результат работы метода объекта, для чего оно использует метод [runAction](https://github.com/cosmastar112/ads_api/blob/master/app/Controller.php#L34) класса Controller. 
В методе runAction перед запуском обработчика выполняется фильтрация HTTP-метода. Конфигурация фильтра задается в конкретном экземпляре контроллера (в методе [allowedMethods](https://github.com/cosmastar112/ads_api/blob/master/controllers/Ads.php#L14)). 
Если метод не поддерживается, будет возвращена [ошибка 405](https://github.com/cosmastar112/ads_api/blob/master/errors/405.html).

В экшене ([создание](https://github.com/cosmastar112/ads_api/blob/master/controllers/Ads.php#L24), [редактирование](https://github.com/cosmastar112/ads_api/blob/master/controllers/Ads.php#L66), [открутка](https://github.com/cosmastar112/ads_api/blob/master/controllers/Ads.php#L110)), в зависимости от элемента, контроллера осуществляется:
* доступ к параметрам запроса (если они есть)
* валидация параметров (если они есть)
* выполнение операции
*	возврат результата.

Для валидации используется [модель](https://github.com/cosmastar112/ads_api/blob/master/models/Ad.php), которая располагается в директории [models](https://github.com/cosmastar112/ads_api/tree/master/models).
В случае ошибки валидации будет возвращена ошибка 400.
Для выполнения операции используется [другой объект](https://github.com/cosmastar112/ads_api/blob/master/models/rep/AdRep.php), в котором инкапсулирована работа с БД (паттерн Репозиторий). Для этого объект реализует [интерфейс](https://github.com/cosmastar112/ads_api/blob/master/app/IRepository.php).
Для работы с БД используется объект [Db](https://github.com/cosmastar112/ads_api/blob/master/app/Db.php) – обёртка над [PDO](https://www.php.net/manual/en/book.pdo.php). Соединение с БД открывается один раз при создании Приложения; доступ к нему предоставляется с помощью метода [getDb](https://github.com/cosmastar112/ads_api/blob/master/app/Application.php#L44). 
В свою очередь, для использования приложения предназначен статический метод [Application::getApp()](https://github.com/cosmastar112/ads_api/blob/master/app/Application.php#L39), который возвращает ссылку на экземпляр Приложения, которая сохраняется при его создании.

## Развитие (TODO)
* Использовать библиотеки для решения задачи (с помощью composer):
  * Роутер
  *	ORM для работы с БД
  *	Тестовый фреймворк
  *	Фреймворк для документирования
  *	Привести код к стандарту PSR-12
* Задокументировать код
* Написать тесты (как минимум, модульные)
*	Развернуть в контейнере (Docker)
* Использовать статический анализатор
*	Новый алгоритм открутки
