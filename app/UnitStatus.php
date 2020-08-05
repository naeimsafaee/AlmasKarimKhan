<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UnitStatus extends Model
{
    public $timestamps = false;
    protected $fillable=['name'];
}
