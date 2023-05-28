# Super Simple DTO

[![Latest Version on Packagist](https://img.shields.io/packagist/v/mohammedmanssour/super-simple-dto.svg?style=flat-square)](https://packagist.org/packages/mohammedmanssour/super-simple-dto)
[![Tests](https://img.shields.io/github/actions/workflow/status/mohammedmanssour/super-simple-dto/run-tests.yml?branch=master&label=tests&style=flat-square)](https://github.com/mohammedmanssour/super-simple-dto/actions/workflows/run-tests.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/mohammedmanssour/super-simple-dto.svg?style=flat-square)](https://packagist.org/packages/mohammedmanssour/super-simple-dto)

Creating data transfer objects with the power of php objects. No php attributes, no reflection api, and no other under the hook work.

This is a laravel package and `laravel/framework` is part of the package dependencies. Please, make sure you have no problem with that before using.

## Why Bother.

The spatie team has already created an awesome package that serves as a [great solution for DTO objects](https://github.com/spatie/laravel-data/blob/main/composer.json). But, for me, it's full of features that I don't use and it seems like an overkill for me when I just wanted simple solution for DTO work.

## Installation

You can install the package via composer:

```bash
composer require mohammedmanssour/super-simple-dto
```

## Usage

1. Apply the `AsDTO` trait to your data object

```php

use MohammedManssour\DTO\Concerns\AsDTO;

class UserData
{
    use AsDTO;

    public string $name;

    public string $email;

    public BalanceData $balance;

    public Status $status;
}
```

2. use on of these static methods to convert data into DTP:
    1. `fromCollection`: converts collections to DTO objects.
    2. `fromArray`: converts array to DTO objects.
    3. `fromModel`: converts model to DTO objects. It works with the data available with `$model->getAttributes()` method.
    4. `fromRequest`: converts laravel requests to DTO objects. It works with the data available with `validated()`. In case `validated` method is not available, it'll use the `all()` method. You can also force using request's `all` method by passing true as a second parameter.

```php
UserData::fromCollection(collect([]));

UserData::fromArray([]);

UserData::fromModel($model);

UserData::fromRequest($request);
```

### How DTO is populated:

The `AsDTO` will assign values to DTO attributes depending of the keys. and attributes that has no key match will not be initialized/assigned

```php
$data = [
    'name' => 'Mohammed Manssour',
    'email' => 'hello@mohammedmanssour.me'
];
$dto = UserData::fromArray();

$this->assertEquals($data['name'], $dto->name);
$this->assertEquals($data['email'], $dto->email);

$this->assertFalse(isset($dto->balance));
$this->assertFalse(isset($dto->status));
```

### Handling special attributes:

In case you have an attribute that needs special care. you can add a method to your dto than have the same name as your attribute and take care of the conversion.

```php
use MohammedManssour\DTO\Concerns\AsDTO;

class BalanceData
{
    use AsDTO;

    public float $bitcoin;

    public int $usdollar;
}

enum Status: string
{
    case Active = 'active';
    case Suspended = 'suspended';
}

class UserData
{
    use AsDTO;

    ....

    public BalanceData $balance;

    public Status $status;

    public function balance($value)
    {
        return $this->balance = BalanceData::fromArray($value);
    }

    public function status($value)
    {
        return $this->status = Status::from($value);
    }
}

$data = [
    'balance' => [
        'bitcoin' => 10,
        'usdollar' => 100
    ],
    'status' => 'active'
];

$dto = UserData::fromArray($data)

$this->assertInstanceOf(BalanceData::class, $dto->balance);
$this->assertEquals($data['balance']['bitcoin'], $dto->balance->bitcoint);
$this->assertEquals($data['balance']['usdollar'], $dto->balance->usdollart);


$this->assertInstanceOf(Status::class, $dto->status);
$this->assertEquals(Status::Active, $dto->status);

```

## converting DTO to array

You can convert dto to array using DTO method

```php
$arr = $dto->toArray()
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](https://github.com/spatie/.github/blob/master/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

-   [Mohammed Manssour](https://github.com/mohammedmanssour)
-   [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
