<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Article extends Model{

    public $created_atp;

    protected $fillable = [
        'article_category_id',
        'title',
        'body',
        'image_id',
        'slug',
        'seo_desc',
        'thumbnail_image_id',
        'admin_id',
        'status',
    ];

    protected $appends = ["image_url" , "shamsi_date" , "article_brief"];

    public function article_category(){
        return $this->belongsTo(ArticleCategory::class);
    }

    public function keywords(){
        return $this->belongsToMany(Keyword::class, 'article_keywords');
    }

    public function thumbnailImage(){
        return $this->belongsTo(Image::class, 'thumbnail_image_id');
    }

    public function image(){
        return $this->hasOne(Image::class, 'id', 'image_id');
    }

    public function getArticleBriefAttribute()
    {
        return mb_substr($this->body , 0 , 200);
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
