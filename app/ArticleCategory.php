<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ArticleCategory extends Model
{
    protected  $fillable=['name','slug','description'];

    public function articles()
    {
        return $this->hasMany(Article::class);
    }
}
