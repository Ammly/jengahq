# Jengahq

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]
[![Build Status][ico-travis]][link-travis]
[![StyleCI][ico-styleci]][link-styleci]

Jengahq is Equity's Jenga API V2 wrapper for Laravel.

> **Note:** This package is still under active development. See [Contributing](#Contributing) to start contributing.

## Installing Jengahq

The recommended way to install Jengahq is through `Composer`

```bash
composer require ammly/jengahq
```

See instructions for installing [Composer](http://getcomposer.org) if you don't have it installed.

## Requirements

This package requires the following

```
"php": ">=5.5.0",
"guzzlehttp/guzzle": "~6.0"
```


## Setup

Register for an accout at [JengaHq](https://jengahq.io/) and obtain your credentials

Follow the [Official Documentation](https://developer.jengaapi.io/docs/generating-signatures) guide on how to generate your ssl keys and upload your public key to your account's [API Keys section](https://test.jengahq.io/#!/developers/api-keys).

Store the keys in your Laravel project's `storage` folder.

### Configuration

Populate the following configs on your `.env` file.

```bash
JENGA_USERNAME=
JENGA_PASSWORD=
JENGA_API_KEY=
JENGA_PHONE=
JENGA_BASE_ENDPOINT=https://uat.jengahq.io

```

## Usage
Include the package on to your file

```php

<?php 

use Ammly\Jengahq\Jengahq;

```

Then you can new up a new instance and send your request.

```php 

$jengahq = new Jengahq;
$jengahq->sendMoney($params);

```

Or use the `Jengahq` Facade

```php

Jenga::sendMoney($params);

```

### generate Token

Use `$jengahq->authenticate()` to generate a token that will be sent as the `Authorization ` header of every request. This is called automatically by all actions in this package.

### Account Balance

Use `$jengahq->accountBalance()` to check account balance.

Sample
```php 
<?php 

use Ammly\Jengahq\Jengahq;

$params = [
    'account_id' => 1100161816677,
    'country_code' => 'KE',
    'date' => date('Y-m-d'),
];

$jenga = new Jengahq;
$jenga->accountBalance($params);

```

### Send Money

Use `$jengahq->sendMoney()` to send money through `Pesalink` or `InternalFundsTransfer` for an Internal Equity account.

Sample
```php
<?php 

use Ammly\Jengahq\Jengahq;

$params = [
    'country_code' => 'KE',
    'date' => date('Y-m-d'),
    'source_name' => 'John Doe',
    'source_accountNumber' => '0001092883',
    'destination_name' => 'Jane Doe',
    'destination_mobileNumber' => '25474738846',
    'destination_bankCode' => 63,
    'destination_accountNumber' => '9200002773',
    'transfer_currencyCode' => 'KES',
    'transfer_amount' => '10',
    'transfer_type' => 'PesaLink', //Or InternalFundsTransfer
    'transfer_reference' => '127364836548',
    'transfer_description' => 'Some description',
];

$jenga = new Jengahq;
$jenga->sendMoney($params);

```

### IPRS Search
Use `$jenga->iprsSearch($params)` to perform an `IPRS` search.

sample
```php 

<?php 

use Ammly\Jengahq\Jengahq;
$params = [
    'country_code' => 'KE',
    'account_id' => '1100161816677';
    'document_type' => 'ID'; // Or Passport
    'first_name' => 'John';
    'last_name' => 'Doe';
    'document_number' => '28663883';
];

$jenga = new Jengahq;
$jenga->iprsSearch($params);

```


## Help and docs

- [Documentation](http://github.com/ammly/jengahq)
- [Official Documentation](https://developer.jengaapi.io/docs/)
- [Issues](https://github.com/ammly/jengahq/issues)

## Change log

Please see the [changelog](changelog.md) for more information on what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [contributing.md](contributing.md) for details and a todolist.

## Security

If you discover any security related issues, please email ammlyf@gmail.com instead of using the issue tracker.

## Credits

- [Ammly][link-author]
- [All Contributors][link-contributors]

## License

MIT. Please see the [license file](license.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/ammly/jengahq.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/ammly/jengahq.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/ammly/jengahq/master.svg?style=flat-square
[ico-styleci]: https://styleci.io/repos/12345678/shield

[link-packagist]: https://packagist.org/packages/ammly/jengahq
[link-downloads]: https://packagist.org/packages/ammly/jengahq
[link-travis]: https://travis-ci.org/ammly/jengahq
[link-styleci]: https://styleci.io/repos/12345678
[link-author]: https://github.com/ammly
[link-contributors]: ../../contributors
