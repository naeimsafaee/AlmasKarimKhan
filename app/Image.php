<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    protected $hidden = ['pivot'];

    protected $fillable = ['image_path','image_original','image_type','thumbnail_path'];
}
