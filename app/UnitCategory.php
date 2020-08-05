<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UnitCategory extends Model{

    public $timestamps = false;

    protected $fillable = ['name'];

    protected $hidden = ["status"];

}
