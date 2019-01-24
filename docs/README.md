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
The way Properties works, a `Property` is created with a `key`, `group`, `type`, `targets`, and `default` where:

- `key` is a unique identifier for the property
- `group` is a tag to group properties together (**optional**)
- `type` is the data type of the value, see the Property Types section (**optional**)
- `targets` is a tag to identify which models this property may be associated to (**optional**)
- `default` is the default value that will be set on the associating model if no value has been set

Once you have created at least one property, you can associate it to any other model with optional custom values, or if no values have been defined, the defaults will be assigned.

## Creating a Property
Creating a property follows the same process as you would create any other model. In our example we'll assume we have a `Person` model, a `MAX_DOWNLOADS_ALLOWED` property and a `API_CONFIG` property:

```php
use Properties\Models\Property;

Property::create([
   'key' => 'MAX_DOWNLOADS_ALLOWED',
   'group' => null,
   'type' => 'INTEGER',
   'targets' => ['PERSON'],
   'default' => '200'
]);

Property::create([
   'key' => 'API_CONFIG',
   'group' => null,
   'type' => 'JSON',
   'targets' => ['PERSON'],
   'default' => ['username' => null, 'password' => null]
]);

```

### Property Types
By default, if a property `type` column is set to `null`, any values will be returned as a string, however some default types have been supplied out of the box:

- INT/INTEGER: Will cast the value to an `integer`.
- BOOL/BOOLEAN: Will cast the value to a `bool`.
- JSON: Will cast a valid JSON value to `JSON`.
- SCHEMA: Will cast the value to a special type of JSON format, see the Schema Property section.

#### Schema Property
To be updated.

### Property Targets
To be updated.

## Attaching a Property to Models
To be updated.
