<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name','slug','image_id','parent_id','group_category_id'];

    public function child_categories()
    {
        return Category::where('parent_id', $this->id)->get();
    }

    public function scopeParentsOnly($query)
    {
        return $query->where('parent_id', null);
    }
}
