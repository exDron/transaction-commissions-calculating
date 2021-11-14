# Transaction commissions calculating service

Service for calculate commissions for already made transactions

## Requirements
- PHP 7.4
- Built-in libcurl support.

## Installation
For running this example, you need to install  [Composer](https://getcomposer.org/) tool before.

run 
```
composer install
```
in the root directory to install all necessary dependencies for this example

## Config
To change API urls, EU countries list or coefficients please edit ```src/config/config.php```

## Usage

```php
    #To run the program execute in the console this command:
    php app.php input.txt
```

## Tests
To run unit tests execute
```php
     php ./vendor/bin/phpunit ./Tests/Service/CommissionCalculatorTest.php
```