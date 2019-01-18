<?php

namespace Properties\Traits;

use Properties\Models\Property;
use Properties\Models\Propertyable;

trait HasProperties
{

    /**
    * Return properties on the inheriting model.
    *
    * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
    */
    public function properties()
    {
        return $this->morphToMany(Property::class, 'propertyable')
                    ->withPivot('value');
    }
}
