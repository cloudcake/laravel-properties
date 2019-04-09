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
}
