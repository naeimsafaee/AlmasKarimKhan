<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;


class Unit extends Authenticatable{

    use Notifiable, HasApiTokens;

    protected $fillable = [
        'name',
        'last_name',
        'email',
        'password',
        'unit_status_id',
        'slide_image_id',
        'description',
        'image_id',
        'unit_category_id',
        'vitrin_image_id',
        'slug',
        'pluck_id',
        'phone_number',
        'postal_code',
    ];

    protected $hidden = [
        "created_at",
        "updated_at",
        "password",
        "unit_status_id",
        "slide_image_id",
        "image_id",
        "unit_category_id",
        "pluck_id",
        "password",
    ];

    protected $appends = ["status" , "image_url" , "vitrin_url"];

    public function getStatusAttribute(){
        return UnitStatus::find($this->unit_status_id)->name;
    }

    public function slide(){
        return $this->hasOne(Image::class, 'id', 'slide_image_id');
    }

    public function image(){
        return $this->hasOne(Image::class, 'id', 'image_id');
    }

    public function gallery(){
        return $this->hasMany(UnitGallery::class, 'unit_id', 'id');
    }

    public function category(){
        return $this->hasOne(UnitCategory::class, 'id', 'unit_category_id');
    }

    public function vitrin(){
        return $this->hasOne(Image::class, 'id', 'vitrin_image_id');
    }

    public function pluck(){
        return $this->hasOne(Pluck::class, 'id', 'pluck_id');
    }

    public function products(){
        return $this->hasMany(Product::class, 'unit_id', 'id')
            ->orderBy('discount' , "DESC");
    }

    public function product(){
        return $this->hasMany(Product::class, 'unit_id', 'id')
            ->orderBy('discount' , "DESC")->take(4);
    }

    public function top_product(){
        return $this->hasMany(Product::class, 'unit_id', 'id')
            ->orderBy('rate' , "DESC")->take(1);
    }

    public function article(){
        return $this->hasMany(UnitArticle::class, 'unit_id', 'id');
    }

    public function getImageUrlAttribute(){
        $image = $this->image;
        unset($this->image);
        return $image;
    }

    public function getVitrinUrlAttribute(){
        $image = $this->vitrin;
        unset($this->vitrin);
        return $image;
    }

    public function discount()
    {
        return $this->hasOne(UnitDiscount::class,'unit_id');
    }

}
