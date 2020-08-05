<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserOrders extends Model{

    use SoftDeletes;

    protected $fillable = ["product_id" , "user_id" , "order_status_id"];

    protected $hidden = [ "updated_at" , "order_status_id"];
    protected $appends = ["status" , "persian_date"];

    public function getStatusAttribute(){
        return @OrderStatus::find($this->order_status_id)->name;
    }

    public function getPersianDateAttribute(){
        if ($this->created_at) {
            $date= @$this->created_at->format('Y-m-d');

            $date = @explode("-", $date);

            return gregorian_to_jalali($date[0], $date[1], $date[2], "/");
        }else{
            return null;
        }
    }

    public function product(){
        return $this->hasOne(Product::class, 'id', 'product_id');
    }

    public function user(){
        return $this->hasOne(User::class, 'id', 'user_id');
    }



}
