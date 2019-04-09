<?php

namespace Properties\Traits;

use Closure;

trait HasProperties
{
    /**
     * Return properties on the inheriting model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function properties()
    {
        return $this->morphToMany(config('properties.model', \Properties\Models\Property::class), 'propertyable')
                    ->using(\Properties\Models\Propertyable::class)
                    ->withPivot('value');
    }

    /**
     * A simplified alias for attaching a property with a custom value.
     *
     * @param mixed $property
     * @param mixed $value
     *
     * @return \Properties\Models\Property
     */
    public function attachProperty($property, $value = null)
    {
        $class = config('properties.model', \Properties\Models\Property::class);

        if (!($property instanceof $class)) {
            $property = $class::find($property);
        }

        if (!$property) {
            throw new \Exception("Property not found");
        }

        if ($property->type == 'JSON' && !is_string($value)) {
            $value = json_encode($value);
        }

        $this->properties()->attach($property->id, ['value' => $value]);

        return $this;
    }
}
