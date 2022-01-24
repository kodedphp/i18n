Koded - i18n
============

[![CI](https://github.com/kodedphp/i18n/actions/workflows/unit-tests.yml/badge.svg)](https://github.com/kodedphp/i18n/actions/workflows/ci.yml)
[![Latest Stable Version](https://img.shields.io/packagist/v/koded/i18n.svg)](https://packagist.org/packages/koded/i18n)
[![Minimum PHP Version: 8.1](https://img.shields.io/badge/php-%3E%3D%208.1-8892BF.svg)](https://php.net/)

A simple internationalization library with support for .po and .php translation files.

    composer require koded/i18n

Translation files
-----------------

### .po files

`poedit`

### .php files

The translation file has a simple structure

```php
return [
    'language' => '',
    'messages' => [
        // your translation strings goes here
    ]
];
```

where `messages` contains `key => value` pairs for the translated strings. 

Code quality
------------

[![Code Coverage](https://scrutinizer-ci.com/g/kodedphp/i18n/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/kodedphp/i18n/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/kodedphp/i18n/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/kodedphp/i18n/?branch=master)

```shell script
vendor/bin/phpunit
```

License
-------

[![Software license](https://img.shields.io/badge/License-BSD%203--Clause-blue.svg)](LICENSE)

The code is distributed under the terms of [The 3-Clause BSD license](LICENSE).

