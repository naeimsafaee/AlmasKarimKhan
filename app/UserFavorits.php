<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserFavorits extends Model{

    protected $fillable = ["user_id" , "product_id"];

    protected $hidden = ["updated_at"];

    public function product()
    {
        return $this->hasOne(Product::class , 'id' , 'product_id');
    }

}
