<?php

namespace Properties\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Properties\Traits\HasProperties;

class Person extends Model
{
    use HasProperties;

    protected $fillable = [
      'name',
    ];
}
