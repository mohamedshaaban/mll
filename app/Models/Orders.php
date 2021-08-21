<?php

namespace App\Models;

use App\User;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class Orders extends Model
{
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */
    const CASH_PAYMENT = 1 ;
    const KNET_PAYMENT = 2;
    const LATE_PAYMENT = 3 ;

    protected $table = 'orders';
    protected $primaryKey = 'id';
    protected $fillable = ['invoice_unique_id', 'customer_id','car_id','area_from','area_to','driver_id','status','address','paid_by',
    'comission','comission_paid','date','time','amount','payment_type','is_paid','payment_link'];
    public function customers()
    {
        return $this->belongsTo(Customers::class, 'customer_id');
    }
    public function paidby()
    {
        return $this->belongsTo(Customers::class, 'paid_by');
    }
    public function cars()
    {
        return $this->belongsTo(Cars::class, 'car_id');
    }
    public function areafrom()
    {
        return $this->belongsTo(Areas::class, 'area_from');
    }
    public function areato()
    {
        return $this->belongsTo(Areas::class, 'area_to');
    }
    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }
    public function requeststatus()
    {
        return $this->belongsTo(RequestStatus::class, 'status');
    }
}