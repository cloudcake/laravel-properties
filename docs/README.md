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

- `INT` or `INTEGER`: Will cast the value to an `integer`.
- `BOOL` or `BOOLEAN`: Will cast the value to a `bool`.
- `JSON`: Will cast a valid JSON value to `JSON`.
- `SCHEMA`: Will cast the value to a special type of `JSON` format, see the Schema Property section.

## Attaching a Property to Models
Once you've created your properties with default values, associating them to any model that contains the `HasProperties` attribute is as simple as calling:

```php
use App\Models\Person;

$person = Person::first();

// Attach the MAX_DOWNLOADS_ALLOWED property with a custom value of 700.
$person->attachProperty('MAX_DOWNLOADS_ALLOWED', 700);

// Attach the MAX_DOWNLOADS_ALLOWED property with the default Property value.
$person->attachProperty('MAX_DOWNLOADS_ALLOWED');
```

## Detaching a Property from Models
Detaching properties is done in the same way regular Laravel detaching is done:

```php
use App\Models\Person;

$person = Person::first();
$person->properties()->detach('MAX_DOWNLOADS_ALLOWED');
```

## Retrieving Properties attached to a model
Since the properties associating are a regular Laravel polymorphic relationship, you can call your eloquent queries as you usually would to retrieve properties:

```php
$john = Person::find(1337);
$john->attachProperty('API_CONFIG', ['username' => 'foobar', 'password' => 'p455w0rd']);
$john->attachProperty('MAX_DOWNLOADS_ALLOWED', 123);

$api = $john->properties()->find('API_CONFIG');

print_r($api->value); // ['username' => 'foobar', 'password' => 'p455w0rd']
print_r($api->default); // ['username' => null, 'password' => null]

$max_downloads = $john->properties()->find('MAX_DOWNLOADS_ALLOWED');

echo $max_downloads->value; // 123
echo $max_downloads->default; // 200

```

## Retrieving Properties by Target
If making use of the `targets` field, you may retrieve only properties that fit the `targets` criteria by appending the `targetting` scope to your queries:

```php
// Get properties targetting 'PERSON'
Property::targeting('PERSON')->get();

// Get properties targetting 'PERSON' or 'ANIMALS' or 'COMPUTERS'
Property::targetting(['PERSON','ANIMALS','COMPUTERS'])->get();

// Get properties on person model targeting 'PERSON'
Person::find(1337)->properties()->targetting('PERSON')->get();

```

## Schema Property
The `SCHEMA` type is a custom pre-configured data type that was created with the need to store application preferences in mind. An example might be a case where you need to store a users theme settings for your web application and you don't want to store several smaller properties for it. This type was configured in such a way that the blueprint could be used to construct a responsive frontend based on the type of values required.

Think of this type as a property containing many sub-properties inside a JSON string. Each object inside the JSON string requires 4 fields:

- `key`: The key for the specific object
- `default`: The default value for the specific object
- `type`: Your own custom type to assist the frontend in identifying what type of input to display
- `label`: A human readable label to describe the object

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
