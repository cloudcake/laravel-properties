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
