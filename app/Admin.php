<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

class Admin extends Authenticatable
{
    protected $fillable = ['name','email','password','phonenumber'];
    protected $guard_name ='admin';
    protected $guard ='admin';

}
