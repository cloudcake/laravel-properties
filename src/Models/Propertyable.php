<?php

namespace Properties\Models;

use Illuminate\Database\Eloquent\Relations\MorphPivot;

class Propertyable extends MorphPivot
{


  /**
   * Mutate the value based on the property type.
   *
   * @param string $value
   *
   * @return mixed
   */
    public function getValueAttribute($value)
    {
        $value = $this->property->getValueAttribute($this->attributes['value'] ?? null);

        return $value;
    }

    public function property()
    {
        return $this->belongsTo(config('properties.model', \Properties\Models\Property::class));
    }
}
