<?php

namespace Properties\Models;

use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    /**
     * The primary key column name.
     *
     * @var array
     */
    public $primaryKey = 'key';

    /**
     * Whether the primary key is incremental.
     *
     * @var bool
     */
    public $increments = false;

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'key'     => 'string',
        'type'    => 'string',
        'targets' => 'json',
    ];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'key',
        'type',
        'targets',
        'default',
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
     * @return array
     */
    public function getValueAttribute()
    {
        $value = $this->pivot->value ?? $this->default;

        if ($this->attributes['type'] == 'SCHEMA') {
            if (!($this->pivot->value ?? false)) {
                return collect($this->default)->keyBy('key')->transform(function ($value) {
                    return $value->default;
                })->all();
            }
        }

        return $this->getDefaultAttribute($value);
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
     * Mutate the value relative to the type..
     *
     * @param string $value
     *
     * @return array
     */
    public function setDefaultAttribute($value)
    {
        switch ($this->attributes['type'] ?? 'JSON') {
          case 'JSON':
              $this->attributes['default'] = json_encode($value);
              break;

          case 'SCHEMA':
              $this->attributes['default'] = json_encode($value);
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
     * Mutate the default value according to its type.
     *
     * @param string $value
     *
     * @return array
     */
    public function getDefaultAttribute($value)
    {
        switch ($this->attributes['type'] ?? 'JSON') {
          case 'JSON':
              $value = !is_array($value) ? json_decode($value) : $value;
              break;

          case 'SCHEMA':
              $value = !is_array($value) ? json_decode($value) : $value;
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
              break;
        }

        return $value;
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
