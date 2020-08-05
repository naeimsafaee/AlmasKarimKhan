<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model{

    protected $fillable = ['name', 'slug', 'image_id', 'parent_id', 'group_category_id'];

    protected $hidden = ["created_at" , "updated_at"];

    public function child_categories(){
        return Category::where('parent_id', $this->id)->get();
    }

    public function scopeParentsOnly($query){
        return $query->where('parent_id', null);
    }

    public function group(){
        return $this->hasOne(GroupCategory::class, 'id', 'group_category_id');
    }

    public function image(){
        return $this->belongsTo(Image::class);
    }
    
    public function products(){
        return $this->hasMany(Product::class);
    }

}
