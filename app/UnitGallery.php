<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UnitGallery extends Model{

    protected $fillable = ['unit_id', 'image_id'];

    protected $hidden = ["created_at", "updated_at", "unit_id", "image_id"];

    protected $appends = ["url"];

    public function getUrlAttribute(){
        $image = Image::find($this->image_id);
        return $image->url;
    }

}
