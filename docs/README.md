<h6 align="center">
    <img src="https://raw.githubusercontent.com/stephenlake/laravel-properties/master/docs/assets/laravel-properties.png?v=2"/>
</h6>

<h6 align="center">
    Associate generic properties/attributes to any model.
</h6>

# Getting Started

## Install the package via composer

```bash
composer require stephenlake/laravel-properties
```

## Register the service provider

This package makes use of Laravel's auto-discovery. If you are an using earlier version of Laravel (&lt; 5.4) you will need to manually register the service provider:

Add `Properties\PropertiesServiceProvider::class` to the `providers` array in `config/app.php`.

## Publish configuration

`php artisan vendor:publish --provider="Properties\PropertiesServiceProvider" --tag="config"`

## Publish migrations

`php artisan vendor:publish --provider="Properties\PropertiesServiceProvider" --tag="migrations"`

## Run migrations

`php artisan migrate`

# Setting Up

The only requirement to start attaching properties to models is to add the `\Properties\Traits\HasProperties`  trait:

```php
use Properties\Traits\HasProperties;

class User extends \Illuminate\Database\Eloquent\Model
{
    use HasProperties;
}
```

That's it. See the usage section for examples.

# Usage

## Creating a Property

```php
use Properties\Models\Property;

Property::make('MAX_DOWNLOADS', 'INT', 200);

Property::make('OTHER_CONFIG', 'ARRAY', [
  'username' => null,
  'password' => null
]);

Property::make('API_CONFIG', 'JSON', [
  'username' => null,
  'password' => null
]);
```

## Attaching a Property to Models

```php
use App\User;

$user = User::first();

// Attach the MAX_DOWNLOADS property with a custom value of 700.
$user->attachProperty('MAX_DOWNLOADS', 700);

// Attach the MAX_DOWNLOADS property with the default Property value.
$user->attachProperty('MAX_DOWNLOADS');
```

## Detaching a Property from Models

```php
// Attach the MAX_DOWNLOADS property with a custom value of 700.
$user->detachProperty('MAX_DOWNLOADS');
```

## Retrieving Properties attached to a model

```php
$user = User::find(1337);

$user->attachProperty('API_CONFIG', ['username' => 'foobar', 'password' => 'p455w0rd']);
$user->attachProperty('OTHER_CONFIG', ['username' => 'foobar', 'password' => 'p455w0rd']);
$user->attachProperty('MAX_DOWNLOADS', 123);

$user->property('API_CONFIG');    // {"username":"foobar","password":"p455w0rd"}
$user->property('OTHER_CONFIG');  // ['username' => 'foobar', 'password' => 'p455w0rd']
$user->property('MAX_DOWNLOADS'); // 123
```
