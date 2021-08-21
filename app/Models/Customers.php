<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class Customers extends Model
{
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

     const CUSTOMER = 1;
     const GARAGE = 2;

     const ACTIVE = 1;
     const BLOCK = 2;

    protected $table = 'customers';
    protected $primaryKey = 'id';
    protected $fillable = ['name', 'mobile','status','type'];

}