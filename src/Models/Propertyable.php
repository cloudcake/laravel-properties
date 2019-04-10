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

    /**
     * Mutate the value based on the property type.
     *
     * @param string $value
     *
     * @return void
     */
    public function setValueAttribute($value)
    {
        switch ($this->type) {
            case 'INT':
            case 'INTEGER':
                $value = intval($value);
                break;

            case 'BOOL':
            case 'BOOLEAN':
                $vlaue = boolval($value);
                break;

            case 'JSON':
            case 'OBJECT':
            case 'ARRAY':
                $value = !is_string($value) ? json_encode($value) : $value;
                break;
        }

        $this->attributes['value'] =  $value;
    }

    /**
     * Property relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function property()
    {
        $propertyModel = config('properties.model', \Properties\Models\Property::class);

        return $this->belongsTo($propertyModel);
    }
}
