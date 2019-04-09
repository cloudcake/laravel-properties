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
        'key'     => 'string',
        'type'    => 'string',
        'targets' => 'array'
    ];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'key',
        'group',
        'type',
        'targets',
        'default'
    ];

    /**
     * The attributes that appended to the model.
     *
     * @var array
     */
    protected $appends = [
        'value',
    ];

    /**
     * Returns the value of the property. Returns the default value
     * if no value has been defined.
     *
     * @return mixed
     */
    public function getValueAttribute($value)
    {
        $type = $this->attributes['type'] ?? 'JSON';

        if (!($value ?? false)) {
            if ($type == 'SCHEMA' || $type == 'JSON') {
                $value = collect($this->default)->keyBy('key')->transform(function ($value) {
                    return $value->default;
                })->all();
            } else {
                $value = $this->default;
            }
        }

        switch ($type) {
            case 'JSON':
                $value = json_decode($value) ?? $value;
                break;
            case 'SCHEMA':
                $value = json_decode($value) ?? $value;
                break;
            case 'INT':
            case 'INTEGER':
                $value = intval($value);
                break;
            case 'BOOL':
            case 'BOOLEAN':
                $value = boolval($value);
                break;
            default:
                $value = $value;
                break;
        }

        return $value;
    }


    /**
     * Mutator fallback for empty targets value.
     *
     * @return mixed
     */
    public function getTargetsAttribute($value)
    {
        $value = is_string($value) ? json_decode($value) ?? $value : $value;

        return $value;
    }

    /**
     * Mutate the value relative to the type..
     *
     * @param string $value
     *
     * @return array
     */
    public function setDefaultAttribute($value)
    {
        $this->attributes['default'] = $value;

        $type = $this->attributes['type'] ?? 'JSON';

        switch ($type) {
            case 'JSON':
                $this->attributes['default'] = json_encode($value) ?? $value;
                break;

            case 'SCHEMA':
                $this->attributes['default'] = json_encode($value) ?? $value;
                break;

            case 'INT':
            case 'INTEGER':
                $this->attributes['default'] = intval($value);
                break;

            case 'BOOL':
            case 'BOOLEAN':
                $this->attributes['default'] = boolval($value);
                break;

            default:
                $this->attributes['default'] = $value;
                break;
        }

        return $this->attributes['default'];
    }

    /**
     * Mutate the key to always be uppercase.
     *
     * @param string $value
     *
     * @return array
     */
    public function setKeyAttribute($value)
    {
        $this->attributes['key'] = strtoupper($value);
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

    /**
     * Scope to only return properties that target an array of items.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array|string                          $targets
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeTargetting($query, $targets = [])
    {
        return $query->whereJsonContains('targets', $targets);
    }

    /**
     * Scope to only find first property by key.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array|string                          $key
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFirstKey($query, $key)
    {
        return $query->where('key', $key)->first();
    }

    /**
     * Create a property using the custom 'schema' type.
     *
     * @param string $query
     * @param array  $targets
     * @param array  $schema
     *
     * @return mixed
     */
    public static function schema(string $key, array $targets, array $schema)
    {
        collect($schema)->each(function ($v, $k) use ($key) {
            $v = (array) $v;

            if (!isset($v['key'])) {
                throw new \Exception("One or more items in '{$key}' do not contain the required 'key' field");
            } elseif (!isset($v['default'])) {
                throw new \Exception("One or more items in '{$key}' do not contain the required 'default' field");
            } elseif (!isset($v['type'])) {
                throw new \Exception("One or more items in '{$key}' do not contain the required 'type' field");
            } elseif (!isset($v['label'])) {
                throw new \Exception("One or more items in '{$key}' do not contain the required 'label' field");
            }
        });

        $model = config('properties.model', \Properties\Models\Property::class);
        $model::create([
          'key'     => $key,
          'targets' => $targets,
          'type'    => 'SCHEMA',
          'default' => $schema,
        ]);
    }
}
