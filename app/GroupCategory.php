<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GroupCategory extends Model{
    protected $fillable = ['name', 'slug', 'image_id'];


    public function categories(){
        return $this->hasMany(Category::class);
    }
}
