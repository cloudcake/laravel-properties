<?php

namespace Properties\Models;

use Illuminate\Database\Eloquent\Relations\MorphPivot;

class Propertyable extends MorphPivot
{
    public function property()
    {
        return $this->belongsTo(config('properties.model', \Properties\Models\Property::class));
    }
}
