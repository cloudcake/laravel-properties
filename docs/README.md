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
use Illuminate\Database\Eloquent\Model;
use Properties\HasProperties;

class Person extends Model
{
    use HasProperties;
}
```

That's it. See the usage section for examples.

# Usage

Once you have created at least one property, you can associate it to any other model with optional custom values. If an association is made with missing fields, the defaults will be returned when retriving the associated property.

## Creating a Property

Creating a property follows the same process as you would create any other model. In our example we'll assume we have a `Person` model, a `MAX_DOWNLOADS` property and a `API_CONFIG` property:

```php
use Properties\Models\Property;

Property::make('MAX_DOWNLOADS', 'INT', 200);

Property::make('API_CONFIG', 'JSON', [
  'username' => null,
  'password' => null
]);
```

## Attaching a Property to Models

Once you've created your properties with default values, associating them to any model that contains the `HasProperties` attribute is as simple as calling `attachProperty` with the name of the property as the first parameter and the overriding values as a second parameter:

```php
use App\User;

$user = User::first();

// Attach the MAX_DOWNLOADS property with a custom value of 700.
$user->attachProperty('MAX_DOWNLOADS', 700);

// Attach the MAX_DOWNLOADS property with the default Property value.
$user->attachProperty('MAX_DOWNLOADS');
```

## Detaching a Property from Models

Detaching properties is done in the same way regular Laravel detaching is done:

```php
use App\User;

$property = Property::whereName('MAX_DOWNLOADS')->first();

$user = User::first();
$user->properties()->detach($property);
// or
$user->properties()->detach($property->id);
```

## Retrieving Properties attached to a model

Since the properties associating are a regular Laravel polymorphic relationship, you can call your eloquent queries as you usually would to retrieve properties:

```php
$john = User::find(1337);

$john->attachProperty('API_CONFIG', ['username' => 'foobar', 'password' => 'p455w0rd']);
$john->attachProperty('MAX_DOWNLOADS', 123);

$john->property('API_CONFIG');    // ['username' => 'foobar', 'password' => 'p455w0rd']
$john->property('MAX_DOWNLOADS'); // 123
```
