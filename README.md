# l5-fixtures

[![Build Status](https://travis-ci.org/mayconbordin/l5-fixtures.svg?branch=master)](https://travis-ci.org/mayconbordin/l5-fixtures)

JSON and CSV fixtures package for Laravel 5.

## Installation

In order to install Laravel 5 Fixtures, just add 

```json
"mayconbordin/l5-fixtures": "dev-master"
```

to your composer.json. Then run `composer install` or `composer update`.

Then in your `config/app.php` add 

```php
'Mayconbordin\L5Fixtures\FixturesServiceProvider'
```

in the `providers` array and

```php
'Fixtures' => 'Mayconbordin\L5Fixtures\FixturesFacade'
```

to the `aliases` array.

## Configuration

To publish the configuration for this package execute `php artisan vendor:publish` and a `fixtures.php` 
file will be created in your `app/config` directory.

## Usage

By default the fixtures directory is `/fixtures`, inside it you should place JSON and/or CSV files with data to fill
the database. The name of the file should be exactly the same as the name of the database. Take a look at the two examples
in the [`/tests_data`](https://github.com/mayconbordin/l5-fixtures/tree/master/tests/_data) directory.

To apply all fixtures to the database run

```php
Fixtures::up();
```

If you only want to apply some fixtures, you can pass an array with the name of the fixtures you want to apply

```php
Fixtures::up('table_one', 'table_two');
```

And to destroy the records in the database run

```php
Fixtures::down();
```

The `down` method can also receive an array with the name of fixtures that will be destroyed. Currently all records
in the database tables are destroyed.

If you haven't published the configuration file or you want to load fixtures from another location, you only need to execute the following code before applying the fixtures:

```php
Fixtures::setUp('/path/to/fixtures');
```

