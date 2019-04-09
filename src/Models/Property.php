<?php

namespace Properties\Models;

use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'default' => 'object'
    ];

    /**
     * The guarded attributes.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * Mutate the value based on the property type.
     *
     * @param string $value
     *
     * @return mixed
     */
    public function getValueAttribute($value)
    {
        $value = ($value ?? ($this->value ?? $this->default));

        if ($this->type == 'INT' || $this->type == 'INTEGER') {
            $value = intval($value);
        } elseif ($this->type == 'BOOL' || $this->type == 'BOOLEAN') {
            $value = boolval($value);
        }

        return $value;
    }

    /**
     * Mutate the type to always be uppercase.
     *
     * @param string $value
     *
     * @return array
     */
    public function setTypeAttribute($value)
    {
        $this->attributes['type'] = strtoupper($value);
    }
}
