# Super Simple DTO

[![Latest Version on Packagist](https://img.shields.io/packagist/v/mohammedmanssour/super-simple-dto.svg?style=flat-square)](https://packagist.org/packages/mohammedmanssour/super-simple-dto)
[![Tests](https://img.shields.io/github/actions/workflow/status/mohammedmanssour/super-simple-dto/run-tests.yml?branch=master&label=tests&style=flat-square)](https://github.com/mohammedmanssour/super-simple-dto/actions/workflows/run-tests.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/mohammedmanssour/super-simple-dto.svg?style=flat-square)](https://packagist.org/packages/mohammedmanssour/super-simple-dto)

Creating data transfer objects with the power of php objects. Simple, lightweight, and efficient DTO conversion with automatic type handling.

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

2. Use one of these static methods to convert data into DTO:
    - `fromCollection`: converts collections to DTO objects.
    - `fromArray`: converts array to DTO objects.
    - `fromModel`: converts model to DTO objects. It works with the data available with `$model->getAttributes()` method.
    - `fromRequest`: converts laravel requests to DTO objects. It works with the data available with `validated()`. In case `validated` method is not available, it'll use the `all()` method. You can also force using request's `all` method by passing true as a second parameter.

```php
UserData::fromCollection(collect([]));
UserData::fromArray([]);
UserData::fromModel($model);
UserData::fromRequest($request);
```

## Features

### Automatic Type Conversion

The package automatically handles type conversion for:
- **Enums**: Automatically converts values to enum instances
- **DTOs**: Automatically converts arrays/objects to other DTO instances
- **Built-in types**: Handles all PHP built-in types

### Array to DTO Collection Mapping with MapInto

The `MapInto` attribute allows you to automatically convert arrays of data into arrays of DTO objects. This is particularly useful when working with collections of related data.

```php
use MohammedManssour\DTO\Concerns\AsDTO;
use MohammedManssour\DTO\Support\MapInto;

class WalletData
{
    use AsDTO;
    
    public string $type;
    public int $balance;
}

class UserData
{
    use AsDTO;
    
    public string $name;
    public string $email;
    
    #[MapInto(WalletData::class)]
    public array $wallets;
}

$data = [
    'name' => 'Mohammed Manssour',
    'email' => 'hello@mohammedmanssour.me',
    'wallets' => [
        ['type' => 'bitcoin', 'balance' => 1000],
        ['type' => 'ethereum', 'balance' => 500],
    ]
];

$user = UserData::fromArray($data);

// $user->wallets will contain an array of WalletData objects
foreach ($user->wallets as $wallet) {
    echo $wallet->type . ': ' . $wallet->balance; // Each wallet is a WalletData instance
}
```

### Custom Property Setters

You can define custom setter methods for properties that need special handling:

```php
class UserData
{
    use AsDTO;
    
    public Carbon $created_at;
    
    public function setCreatedAt($value)
    {
        $this->created_at = Carbon::parse($value);
    }
}
```

### Type Safety

The package respects PHP type declarations and automatically converts:

```php
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
    
    public BalanceData $balance;  // Automatically converted from array
    public Status $status;        // Automatically converted from string
}

$data = [
    'balance' => [
        'bitcoin' => 10.5,
        'usdollar' => 1000
    ],
    'status' => 'active'
];

$dto = UserData::fromArray($data);

// $dto->balance is a BalanceData instance
// $dto->status is a Status enum instance
```

## Property Initialization Check

The package provides a helpful `isset()` method that checks if a property was actually initialized (different from PHP's native `isset()` which returns false for `null` values):

```php
$dto = UserData::fromArray([
    'name' => 'Mohammed',
    'email' => null  // explicitly set to null
]);

// Native PHP isset
isset($dto->name);     // true
isset($dto->email);    // false (because it's null)
isset($dto->balance);  // false (not initialized)

// DTO isset method
$dto->isset('name');     // true
$dto->isset('email');    // true (was explicitly set, even though null)
$dto->isset('balance');  // false (not initialized)
```

## Converting DTO to Array

You can convert any DTO back to an array:

```php
$dto = UserData::fromArray($data);
$array = $dto->toArray();
```

The `toArray()` method handles:
- Nested DTOs (converts them to arrays recursively)
- Enums (converts to their scalar values)
- Carbon instances (keeps as Carbon objects)
- Arrays of DTOs (converts each DTO to array)

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