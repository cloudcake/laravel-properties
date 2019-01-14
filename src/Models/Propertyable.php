<?php

namespace Properties\Models;

use Illuminate\Database\Eloquent\Relations\MorphPivot;

class Propertyable extends MorphPivot
{
    protected $casts = [
      'values'  => 'json',
    ];
}
