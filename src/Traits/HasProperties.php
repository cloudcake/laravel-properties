<?php

namespace Properties\Traits;

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
            if (is_string($property)) {
                $property = $class::where('name', $property)->first();
            } else {
                $property = $class::find($property);
            }
        }

        if (!$property) {
            throw new \Exception('Property not found');
        }

        if ($property->type == 'JSON' && !is_string($value)) {
            $value = json_encode($value);
        }

        $this->properties()->attach($property->id, ['value' => $value]);

        return $this;
    }

    /**
     * Returns the first association of the provided property name with
     * casted values.
     *
     * @param string  $name
     * @param boolean $toArray
     *
     * @return mixed
     */
    public function property($name, $jsonToObject = true)
    {
        $property = $this->properties()->where('name', $name)->first();

        $value = $property->value ?? null;

        if ($value && $jsonToObject && $property->type == 'JSON') {
            $value = json_decode($value);
        }

        return $value;
    }
}
