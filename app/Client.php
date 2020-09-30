<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $guarded = [];

    // store client phones as an array
    protected $casts = [
      'phone' => 'array'
    ];
}
