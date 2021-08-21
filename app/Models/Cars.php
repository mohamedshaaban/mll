<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class Cars extends Model
{
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'cars';
    protected $primaryKey = 'id';
    protected $fillable = ['car_plate_id', 'car_model','car_type_id','customer_id'];
    protected $appends=['customername','cartypesname'];
    public function customers()
    {
        return $this->belongsTo(Customers::class, 'customer_id');
    }
    public function cartypes()
    {
        return $this->belongsTo(CarTypes::class, 'car_type_id');
    }
    public function getCustomernameAttribute()
    {
        if($this->customers)
        {
            return $this->customers->name;
        }
        return '--';
    }
    public function getCartypesnameAttribute()
    {
        if($this->cartypes)
        {
            return $this->cartypes->name_en;
        }
        return '--';
    }
}