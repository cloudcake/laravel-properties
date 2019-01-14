<?php

namespace Properties\Traits;

use Properties\Models\Property;
use Properties\Models\Propertyable;

trait HasProperties
{
    public function properties()
    {
        return $this->morphToMany(Property::class, 'propertyable')
                    ->using(Propertyable::class)
                    ->withPivot('values');
    }
}
