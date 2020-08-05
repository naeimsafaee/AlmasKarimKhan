<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UnitDiscount extends Model{

    protected $fillable = ['unit_id', 'discount'];
    protected $hidden = [
        "created_at",
        "updated_at",
    ];

    public function unit(){
        return $this->hasOne(Unit::class, 'id', 'unit_id');
    }

}
