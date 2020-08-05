<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pluck extends Model
{
    public $timestamps = false;

    protected $fillable=['floor' , 'number'];
}
