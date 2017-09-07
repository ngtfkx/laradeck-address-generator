# Laradeck Address Generator

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]

Генерация реальных адресов для нужд разработки. 

В отличии от Faker адреса реальныe для каждого города и их можно выводить на карту. 
Теперь у тестировщика не будет вопросов - почему 
маркер на карте не совпадает с адресом и как это объяснить клиенту/заказчику.

## Install

Via Composer

``` bash
$ composer require ngtfkx/laradeck-address-generator
```

Если необходимы консольные команды или подключение своих источников данных, то добавьте в файл `config/app.php` (для версий <=5.4.*) 
сервис-провайдер

``` php
Ngtfkx\Laradeck\AddressGenerator\LaradeckAddressGeneratorServiceProvider::class,
```

## Usage

Получение случайного адреса для любого из городов, который есть в пакете

``` php
$generator = new \Ngtfkx\Laradeck\AddressGenerator\Generator();
$address = $generator->getRandomAddress();
```

Получение случайного адреса для любого из указанных городов

``` php
$generator = new \Ngtfkx\Laradeck\AddressGenerator\Generator();
$generator->setCities('Tomsk', 'nsk', 'Омск'); // можно передавать массив
$generator->addCity('новосибирск'); // добавит город к ранее установленным
$address = $generator->getRandomAddress(); // адрес будет для какого-то из вышеуказанных 4-х горолдов
```

## Documentation

### Получение адреса

Для получения адреса существуют следующие методы

- `getRandomAddress($forCity = null): Address` - получить один адрес
- `getRandomAddresses(int $count, $forCity = null): Collection` - получить коллекцию с указанным кол-ом адресов

По умолчанию случайный адрес может быть сгенерирован для любого из поддерживаемых городов (если алиас города не указан параметром)

### Установка города

Если надо сгенерировать адрес для конкретного города(ов), то надо принудительно указать эти города. Для этого есть следующие методы

- `addCity(string $city): Generator` - добавить город по его алиасу в список использованных
- `addCities(...$cities): Generator` - добавить несколько городов (можно через запятую, можно массивом)
- `setCities(...$cities): Generator` - добавить несколько городов, обнулись ранее добавленные

### Объект типа Address

Содержить следующие методы

- `getFull(): string` - получить полный адрес (с городом, улицей и номером дома)
- `getInsideLocality(): string` - получить адрес нутри населенного пункта (только улица и номер дома)
- `getLocality(): string` - получить наименование населенного пункта
- `getStreet(): string` - получить наименование улицы
- `getBuilding(): string` - получить номер дома

### Структура хранения данных

Данные для генерации адресов по городам храняться в папке `data/ru` в файле `{cityId}.php` в 
виде массива, где `{cityId}` - id города в системе http://nominatim.openstreetmap.org/

``` php
return [
    "1 Восточный спуск" => ["100", "101", "102"],
    "1-й квартал" => ["13", "2a", "3/1", "4", "5", "6"],
];
```

Информация о списке поддерживаемых городов и их алиасах хранится в файле `data/ru/cities.php`  виде массива

``` php
return [
    '173343488' => [
        'Омск',
        'Omsk',
    ],
    '173436661' => [
        'Новосибирск',
        'Nsk',
        'Novo-sibirsk',
        'Нск',
    ],
];
```

### Подключение пользовательских данных

Для подключения пользовательских данных положите файл аналогичной структуры (см. Структура хранения данных)
в папку storage (или любую ее подпапку). Далее подключаете данные следующим образом

``` php
$generator = new Generator();
$generator->loadCustomData(111, 'Нефтебаза', 'app/111.php');
```

Теперь адреса населенного пункта Нефтебаза доступны для генерации.

### Статистика

Для получения информации о доступных городах и адресах есть консольная команда
`php artisan address:stat`

### Генерация своих файлов данных

Для генерации файла данных есть консольная команда 
`php artisan address:city-address-ru {city} {url} {--limit=0}`

- {city} - id города в системе http://nominatim.openstreetmap.org/
- {url} - первая страница города со списком улиц на сайте http://www.city-address.ru/, например [вот такая](http://www.city-address.ru/region-70_tomsk/all-street/)
- {--limit=0} - кол-во обрабатываемых страниц (0 - все)

## Поддерживаемые города

- Омск. Адресов: 27 297
- Новосибирск. Адресов: 41 052
- Красноярск. Адресов: 14 628
- Барнаул. Адресов: 16 668
- Томск. Адресов: 17 859

## Города планируемые к добавлению

[Task #4](https://github.com/ngtfkx/laradeck-address-generator/issues/4)

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Credits

- [Denis Sandal][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/ngtfkx/laradeck-address-generator.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://scrutinizer-ci.com/g/ngtfkx/laradeck-address-generator/badges/build.png?b=master
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/ngtfkx/laradeck-address-generator.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/ngtfkx/laradeck-address-generator.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/ngtfkx/laradeck-address-generator.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/ngtfkx/laradeck-address-generator
[link-travis]: https://scrutinizer-ci.com/g/ngtfkx/laradeck-address-generator
[link-scrutinizer]: https://scrutinizer-ci.com/g/ngtfkx/laradeck-address-generator/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/ngtfkx/laradeck-address-generator
[link-downloads]: https://packagist.org/packages/ngtfkx/laradeck-address-generator
[link-author]: https://github.com/ngtfkx
[link-contributors]: ../../contributors
