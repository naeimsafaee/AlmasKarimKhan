<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductToImage extends Model{

    protected $fillable = ["product_id" , "image_id"];

    protected $hidden = ["product_id" , "image_id" , "created_at" , "updated_at"];

    protected $appends = ["url"];

    public function image(){
        return $this->hasOne(Image::class, 'id', 'image_id');
    }

    public function getUrlAttribute(){

        $control = Control::where("title", "Url")->get()[0];
        $this->image;
        $url = $this->image->url;

        unset($this->image);
        return $url;
    }
}
