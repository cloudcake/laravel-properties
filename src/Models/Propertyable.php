<?php

namespace Properties\Models;

use Illuminate\Database\Eloquent\Relations\MorphPivot;

class Propertyable extends MorphPivot
{
    public function getValueAttribute($value)
    {
        $value = $value ??  $this->property->default;

        if ($this->type == 'INT' || $this->type == 'INTEGER') {
            $value = intval($value);
        } elseif ($this->type == 'BOOL' || $this->type == 'BOOLEAN') {
            $value = boolval($value);
        } else {
            $value = json_decode($value) ?? $value;
        }

        return $value;
    }

    public function property()
    {
        return $this->belongsTo(config('properties.model', \Properties\Models\Property::class));
    }
}
