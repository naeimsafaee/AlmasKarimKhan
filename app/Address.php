<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Address extends Model{

    protected $hidden = [
        "created_at",
        "updated_at",
    ];

    protected $appends = ["user_address"];

    protected $fillable = ["address"];

    public function province(){
        return $this->hasOne(Province::class, 'id', 'province_id');
    }

    public function city(){
        return $this->hasOne(City::class, 'id', 'city_id');
    }

    public function getUserAddressAttribute(){
        return $this->province["name"] . " - " . $this->city["name"]
            . " - " . $this->address . " - " . $this->number ;
    }


}
