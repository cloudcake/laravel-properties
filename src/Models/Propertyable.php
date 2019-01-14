<?php

namespace Properties\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class Propertyable extends Pivot
{
    protected $casts = [
      'values'  => 'json',
    ];
}
