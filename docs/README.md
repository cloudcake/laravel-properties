<h6 align="center">
    <img src="https://raw.githubusercontent.com/stephenlake/laravel-properties/master/docs/assets/laravel-properties.png" width="450"/>
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

# Setting Up
The only requirement to start attaching properties to models is to add the `\Properties\HasProperties`  trait:

```php
use Illuminate\Database\Eloquent\Model;
use Properties\HasProperties;

class Person extends Model
{
    use HasProperties;
}
```
That's it. See the usage section for examples.

# Usage

## Creating a Property
To be updated.

### Property Types
To be updated.

### Property Targets
To be updated.

## Creating a Property
To be updated.

## Attaching a Property to Models
To be updated.
