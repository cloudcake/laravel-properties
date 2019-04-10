<?php

namespace Properties\Models;

use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    /**
     * The guarded attributes.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * Create a property.
     *
     * @param string $name
     * @param string $type
     * @param mixed  $params
     *
     * @return self
     */
    public static function make(string $name, string $type = 'JSON', $params = [])
    {
        $type = strtoupper($type);

        if ($type == 'JSON'&& !is_string($params)) {
            $params = json_encode($params);
        }

        return self::create([
            'name'    => $name,
            'type'    => $type,
            'default' => $params
        ]);
    }

    /**
     * Mutate the value based on the property type.
     *
     * @param string $value
     *
     * @return mixed
     */
    public function getValueAttribute($value)
    {
        $value = ($value ?? ($this->getOriginal('pivot_value') ?? $this->default));

        switch ($this->type) {
            case 'INT':
            case 'INTEGER':
                $value = intval($value);
                break;

            case 'BOOL':
            case 'BOOLEAN':
                $value = boolval($value);
                break;

            case 'JSON':
                $value = json_encode(array_merge(json_decode($this->default, true), json_decode($value, true)), JSON_PRETTY_PRINT);
                break;

            case 'OBJECT':
                $value = (object) array_merge(json_decode($this->default, true), json_decode($value, true));
                break;

            case 'ARRAY':
                $value = array_merge(json_decode($this->default, true), json_decode($value, true));
                break;
        }

        return $value;
    }

    /**
     * Mutate the type to always be uppercase.
     *
     * @param string $value
     *
     * @return void
     */
    public function setTypeAttribute($value)
    {
        $this->attributes['type'] = strtoupper($value);
    }
}
