<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    public  $created_atp;
    protected  $fillable=[
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

    public function article_category()
    {
        return $this->belongsTo(ArticleCategory::class);
    }

    public function keywords()
    {
        return $this->belongsToMany(Keyword::class,'article_keywords');
    }
    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function image()
    {
        return $this->belongsTo(Image::class);
    }

    public function thumbnailImage()
    {
        return $this->belongsTo(Image::class,'thumbnail_image_id');
    }
}
