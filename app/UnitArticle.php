<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UnitArticle extends Model{

    protected $fillable = ['unit_id', 'title', 'body', 'image_id', 'thumbnail_image_id', 'seo_desc', 'status'];

    protected $appends = ['keywords' , "image_url" , "shamsi_date"];

    public function getKeywordsAttribute(){
        return UnitArticleKeyword::where("unit_article_id", $this->id)->get();
    }

    public function image(){
        return $this->hasOne(Image::class, 'id', 'image_id');
    }

    public function getImageUrlAttribute(){
        $image = $this->image;
        unset($this->image);
        return $image;
    }

    public function getShamsiDateAttribute(){

        $date = $this->created_at;
        $date = explode(" " , $date)[0];
        $date = explode("-", $date);

        return gregorian_to_jalali($date[0], $date[1], $date[2], "/");
    }

}
