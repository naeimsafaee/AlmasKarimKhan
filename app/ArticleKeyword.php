<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ArticleKeyword extends Model{

    public $timestamps = false;

    protected $table = "article_keywords";

    protected $fillable = ["article_id" , "keyword_id"];

}
