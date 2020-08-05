<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductAttribute extends Model{
    protected $fillable = [
        "product_id",
        "attribute_id",
        "group_attribute_id",
        "value"
    ];

    protected $hidden = [
        "created_at",
        "updated_at",
        "product_id",
        "attribute_id",
        "group_attribute_id",
    ];

    public function attribute(){
        return $this->hasOne(Attribute::class, 'id', 'attribute_id');
    }

    public function getNameAttribute()
    {
        if(is_numeric($this->value)){
            //dd($this->value);
            $attributeOption = AttributeOption::find($this->value);
            //dd($attributeOption);
            return $attributeOption['attribute_option_value'];
        }else{
            return $this->value;
        }
    }

    public function scopeNumericvalue($query)
    {
        return $query->whereHas('attribute',function ($q){
            $q->where('type',2);
        })->whereRaw("value REGEXP '^[0-9]*$'");
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
