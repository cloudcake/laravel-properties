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
                    ->withPivot('value');
    }

    /**
    * A simplified alias for attaching a property with a custom value.
    *
    * @return \Properties\Models\Property
    */
    public function attachProperty($propertyKey, $value)
    {
        $this->properties()->attach($propertyKey, ['value' => $value]);

        return $this;
    }
}
