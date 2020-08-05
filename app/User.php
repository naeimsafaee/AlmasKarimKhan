<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable{
    use Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable = [
        'name',
        'last_name',
        'email',
        'password',
        "mobile",
        "image_id",
        "postal_code",
        "default_address_id",
        "personal_code",
        "home_number",
        "city_id",
        "province_id",
        "birthday",
        "gender",
        "status",
    ];

    /**
     * The attributes that should be hidden for arrays.
     * @var array
     */

    protected $hidden = [
        "gender",
        'password',
        'remember_token',
        "created_at",
        "updated_at",
        "phone_verfied_at",
        "default_address_id"
    ];

    protected $appends = ["full_name", "user_gender", "default_user_address"];

    /**
     * The attributes that should be cast to native types.
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getFullNameAttribute(){
        return $this->name . " " . $this->last_name;
    }

    public function getUserGenderAttribute(){
        return $this->gender == 0 ? "مرد" : "زن";
    }

    public function getDefaultUserAddressAttribute(){

        $address = Address::find($this->default_address_id);
        if($address == null)
            return "null";
        return $address->user_address;
    }
}
