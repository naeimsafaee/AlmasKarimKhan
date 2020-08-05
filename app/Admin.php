<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class Admin extends Authenticatable
{
    use Notifiable, HasApiTokens;

    protected $fillable = ['name','email','password','phonenumber'];
//    protected $guard_name ='admin';
//    protected $guard ='admin';

}
