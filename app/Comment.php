<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model{

    protected $fillable = ["text", "product_id", "unit_id",  "user_id", "rate", "is_active" , "reply_to"];
    protected $appends=['full_name' , 'shamsi_date'];

    public function product(){
        return $this->hasOne(Product::class, 'id', 'product_id');
    }

    public function getFullNameAttribute()
    {
        $user=User::find($this->user_id);
        return $user->name . ' ' . $user->last_name;
    }
    public function getShamsiDateAttribute()
    {
        $date=$this->created_at->format('Y-m-d');

        $date = explode("-", $date);
        return gregorian_to_jalali($date[0], $date[1], $date[2], "/");
    }

    public function user(){
        return $this->hasOne(User::class, 'id', 'user_id');
    }
    public function reply_to(){
        return $this->hasOne(Comment::class, 'id', 'reply_to');
    }

}
