<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AttributeOption extends Model{

    protected $hidden = [
        "created_at",
        "updated_at",
        "attribute_id",
    ];

    protected $fillable = [
        "attribute_id",
        "attribute_option_value",
    ];

    public function attribute(){
        return $this->belongsTo(Attribute::class);
    }

}
