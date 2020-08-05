<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Attribute extends Model{

    protected $hidden = [
        "created_at",
        "updated_at",
    ];

    protected $fillable = [
        "name",
        "type",
        "description",
        "group_attribute_id",
    ];

    public function group_attribute(){
        return $this->hasOne(GroupAttribute::class,'id', 'group_attribute_id');
    }

    public function attributeOption(){
        return $this->hasMany(AttributeOption::class);
    }

}
