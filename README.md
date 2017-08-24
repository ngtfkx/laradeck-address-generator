# Laradeck Address Generator

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]

Генерация реальных адресов для нужд разработки. 

В отличии от Faker адреса реальныt для каждого города и их можно выводить на карту. 
Теперь у тестировщика не будет вопросов - почему 
маркер на карте не совпадает с адресом и как это объяснить клиенту/заказчику.

## Install

Via Composer

``` bash
$ composer require ngtfkx/laradeck-address-generator ~0.1
```

## Usage

Получение случайного адреса для любого из городов, который есть в пакете

``` php
$generator = new Generator();
$address = $generator->getRandomAddress();
```

Получение случайного адреса для любого из указанных городов

``` php
$generator = new Generator();
$generator->setCities('Tomsk', 'nsk', 'Омск'); // можно передавать массив
$generator->addCity('новосибирск'); // добавит город к ранее установленным
$address = $generator->getRandomAddress(); // адрес будет для какого-то из вышеуказанных 4-х горолдов
```

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CODE_OF_CONDUCT](CODE_OF_CONDUCT.md) for details.

## Security

If you discover any security related issues, please email den.sandal@gmail.com instead of using the issue tracker.

## Credits

- [Denis Sandal][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/ngtfkx/laradeck-address-generator.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/ngtfkx/laradeck-address-generator/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/ngtfkx/laradeck-address-generator.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/ngtfkx/laradeck-address-generator.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/ngtfkx/laradeck-address-generator.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/ngtfkx/laradeck-address-generator
[link-travis]: https://travis-ci.org/ngtfkx/laradeck-address-generator
[link-scrutinizer]: https://scrutinizer-ci.com/g/ngtfkx/laradeck-address-generator/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/ngtfkx/laradeck-address-generator
[link-downloads]: https://packagist.org/packages/ngtfkx/laradeck-address-generator
[link-author]: https://github.com/ngtfkx
[link-contributors]: ../../contributors
