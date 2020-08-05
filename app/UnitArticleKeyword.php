<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UnitArticleKeyword extends Model{

    protected $fillable = ['unit_article_id', 'keyword'];

    protected $hidden = ['created_at' , 'updated_at'];
}
