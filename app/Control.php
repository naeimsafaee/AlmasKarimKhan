<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Control extends Model{

    protected $fillable = ["title" , "opt" , "opt_1"];

    protected $hidden = ["created_at" , "updated_at"];

}
