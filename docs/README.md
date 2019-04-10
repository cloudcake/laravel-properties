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

Once you have created at least one property, you can associate it to any other model with optional custom values. If an association is made with missing fields, the defaults will be returned when retriving the associated property.

## Creating a Property

Creating a property follows the same process as you would create any other model. In our example we'll assume we have a `Person` model, a `MAX_DOWNLOADS_ALLOWED` property and a `API_CONFIG` property:

```php
use Properties\Models\Property;

Property::make('MAX_DOWNLOADS_ALLOWED', 'INT', 200);

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

// Attach the MAX_DOWNLOADS_ALLOWED property with a custom value of 700.
$user->attachProperty('MAX_DOWNLOADS_ALLOWED', 700);

// Attach the MAX_DOWNLOADS_ALLOWED property with the default Property value.
$user->attachProperty('MAX_DOWNLOADS_ALLOWED');
```

## Detaching a Property from Models

Detaching properties is done in the same way regular Laravel detaching is done:

```php
use App\User;

$property = Property::whereName('MAX_DOWNLOADS_ALLOWED')->first();

$user = User::first();
$user->properties()->detach($property);
```

## Retrieving Properties attached to a model

Since the properties associating are a regular Laravel polymorphic relationship, you can call your eloquent queries as you usually would to retrieve properties:

```php
$john = User::find(1337);
$john->attachProperty('API_CONFIG', ['username' => 'foobar', 'password' => 'p455w0rd']);
$john->attachProperty('MAX_DOWNLOADS_ALLOWED', 123);

$api = $john->properties()->find('API_CONFIG');

print_r($api->value); // ['username' => 'foobar', 'password' => 'p455w0rd']

$max_downloads = $john->properties()->find('MAX_DOWNLOADS_ALLOWED');

echo $max_downloads->value; // 123
```

## Retrieving Properties by Target

If making use of the `targets` field, you may retrieve only properties that fit the `targets` criteria by appending the `targetting` scope to your queries:

```php
// Get properties targetting 'PERSON'
Property::targetting('PERSON')->get();

// Get properties targetting 'PERSON' or 'ANIMALS' or 'COMPUTERS'
Property::targetting(['PERSON','ANIMALS','COMPUTERS'])->get();

// Get properties on person model targeting 'PERSON'
Person::find(1337)->properties()->targetting('PERSON')->get();
```

## Schema Property

The `SCHEMA` type is a custom pre-configured data type that was created with the need to store application preferences in mind. An example might be a case where you need to store a users theme settings for your web application and you don't want to store several smaller properties for it. This type was configured in such a way that the blueprint could be used to construct a responsive frontend based on the type of values required.

Think of this type as a property containing many sub-properties inside a JSON string. Each object inside the JSON string requires 4 fields:

-   `key`: The key for the specific object
-   `default`: The default value for the specific object
-   `type`: Your own custom type to assist the frontend in identifying what type of input to display
-   `label`: A human readable label to describe the object

See a real world example of one of my own personal projects below:

```php
[{
  "key": "THEME",
  "group": "PREFERENCES",
  "type": "SCHEMA",
  "targets": [
    "USER"
  ],
  "default": [{
      "key": "NAVBAR_TEXT_COLOUR",
      "type": "COLOUR",
      "label": "Navigation Text Colour",
      "default": "#777777"
    },
    {
      "key": "NAVBAR_BACKGROUND_COLOUR",
      "type": "COLOUR",
      "label": "Navigation Background Colour",
      "default": "#FFFFFF"
    },
    {
      "key": "SIDEBAR_TEXT_COLOUR",
      "type": "COLOUR",
      "label": "Sidebar Text Colour",
      "default": "#FFFFFF"
    },
    {
      "key": "SIDEBAR_BACKGROUND_COLOUR",
      "type": "COLOUR",
      "label": "Sidebar Background Colour",
      "default": "#18273E"
    }
  ]
}]
```

### Creating a Schema Property

For this property type only, there's a method to create the property, this is to ensure the input contains all the necessary required fields in order to function correctly. Let's use the above example to create a schema:

```php
// Arguments: KEY, TARGETS, DEFAULT
Property::schema('THEME', ['USER'], [
   [
        [key] => NAVBAR_TEXT_COLOUR
        [type] => COLOUR
        [label] => Navigation Text Colour
        [default] => #777777
   ],
   [
        [key] => NAVBAR_BACKGROUND_COLOUR
        [type] => COLOUR
        [label] => Navigation Background Colour
        [default] => #FFFFFF
   ],
   [
        [key] => SIDEBAR_TEXT_COLOUR
        [type] => COLOUR
        [label] => Sidebar Text Colour
        [default] => #FFFFFF
   ],
   [
        [key] => SIDEBAR_BACKGROUND_COLOUR
        [type] => COLOUR
        [label] => Sidebar Background Colour
        [default] => #18273E
   ]
]);
```

### Assigning a Schema Property to a model

Assigning a schema property is slightly different to that of a regular property because for each object inside the default value we need to set a key with a value and for any missing keys we'll set the default value.

```php
Person::find(1337)->attachProperty('THEME', [
   'NAVBAR_TEXT_COLOUR' => '#FFFFFF',
   'SIDEBAR_BACKGROUND_COLOUR' => '#000000'
]);

$theme = Person::find(1337)->properties()->find('THEME');

print_r($theme->values);

// Results in:
[
     "NAVBAR_TEXT_COLOUR" => "#FFFFFF",
     "NAVBAR_BACKGROUND_COLOUR" => "#FFFFFF", // default
     "SIDEBAR_TEXT_COLOUR" => "#FFFFFF",      // default
     "SIDEBAR_BACKGROUND_COLOUR" => "#000000",
]
```
