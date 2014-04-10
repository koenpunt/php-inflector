PHP Inflector [![Build Status](https://travis-ci.org/koenpunt/php-inflector.svg?branch=master)](https://travis-ci.org/koenpunt/php-inflector)
=============

PHP Inflector Library, ported from [Rails](https://github,com/rails/rails)


## Minimum Requirements

* PHP 5.4+ (support for PHP 5.3 in [php-5.3](https://github.com/koenpunt/php-inflector/tree/php-5.3) branch)
* PHP [Internationalization extension](http://www.php.net/manual/en/book.intl.php) (`php5-intl`)

## Installation

The easiest way to use PHP Inflector is by installing it with [Composer](https://getcomposer.org/)

Create or update `composer.json`:
```json
{
  "require": {
    "koenpunt/php-inflector": "1.0.*"
  }
}
```

And run `composer install`

## Features

* Basics:
    * singularize
    * pluralize
    * singularize
    * camelize
    * underscore
    * humanize
    * titleize
    * tableize
    * classify
    * dasherize
    * denamespace
    * foreign_key
    * ordinalize

* Internationalization
    * transliterate
    * parameterize


## Usage

PHP Inflector is implemented as class with static functions

```php
Inflector::underscore("PhpInflector\Inflector"); # => php_inflector/inflector

PhpInflector\Inflector::parameterize("Ærøskøbing on Water"); # => aeroskobing-on-water

PhpInflector\Inflector::foreign_key("Admin\Post"); # => post_id

PhpInflector\Inflector::denamespace("PhpInflector\Inflector\Inflections"); # => Inflections

PhpInflector\Inflector::dasherize("puni_puni"); # => puni-puni
```

It is also possible to add custom inflections, like acronyms:

```php
PhpInflector\Inflector::inflections(function($inflect){
  $inflect->acronym('RESTful');
});

PhpInflector\Inflector::titleize('RESTfulController'); # => RESTful Controller
```

More examples and documentation can be found in the source.

## Contributing

1. Fork it
2. Create your feature branch (`git checkout -b my-new-feature`)
3. Commit your changes (`git commit -am 'Added some feature'`)
4. Push to the branch (`git push origin my-new-feature`)
5. Create new Pull Request