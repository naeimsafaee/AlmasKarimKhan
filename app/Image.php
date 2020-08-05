<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Image extends Model{

    protected $hidden = [
        "created_at",
        "updated_at",
        "pivot",
        "image_path",
        "image_original",
        "image_type",
        "thumbnail_path",
        "id",
    ];

    protected $fillable = ['image_path', 'image_original', 'image_type', 'thumbnail_path'];

    protected $appends = ["url"];

    public function getUrlAttribute(){

        $control = Control::where("title", "Url")->get()[0];

        return $control->opt . $this->image_path;
    }

}
