<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GroupAttribute extends Model{

    protected $fillable = [
        "name",
        "category_id",
        "description",
        "status",
    ];

    public function attributes()
    {
        return $this->hasMany(Attribute::class);
    }

    public function category()
    {
        return $this->hasOne(Category::class , 'id' , 'category_id');

    }

}
