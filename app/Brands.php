<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Brands extends Model{

    protected $hidden = ["created_at" , "updated_at"];

    protected $appends = ["image_url"];

    public function image(){
        return $this->hasOne(Image::class, 'id', 'image_id');
    }

    public function getImageUrlAttribute(){
        $image = $this->image;
        unset($this->image);
        return $image;
    }

}
