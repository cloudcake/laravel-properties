<?php

namespace Properties\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Property extends Model
{
    use SoftDeletes;

    public $primaryKey = 'key';
    public $increments = false;

    protected $casts = [
      'key'     => 'string',
      'targets' => 'json',
      'schema'  => 'json',
    ];

    protected $appends = [
      'values'
    ];

    public function setKeyAttribute($value)
    {
        $this->attributes['key'] = strtoupper($value);
    }

    public function setTargetsAttribute($value)
    {
        if (is_string($value)) {
            $value = explode(',', $value);
        }

        $this->attributes['targets'] = $value ? json_encode($value) : null;
    }

    public function scopeType($query, $key)
    {
        return $query->where('key', $key);
    }

    public function scopeTargetting($query, $targets = [])
    {
        if (is_string($targets)) {
            $targets = explode(',', $targets);
        }

        return $query->whereJsonContains('targets', $targets);
    }
}
