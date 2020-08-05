<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AttributePrice extends Model{

    protected $hidden = [
        "created_at",
        "updated_at",
        "api_url",
    ];

    protected $fillable = [
        "attribute_id",
        "price",
        "api_url",
        "attribute_option_id",
    ];

}
