<?php

namespace App\Models;

use App\User;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class OrdersHistory extends Model
{
    use CrudTrait;



    protected $table = 'orders_history';
    protected $primaryKey = 'id';
    protected $fillable = ['order_id', 'user_id','field','old_value','new_value','text'];

    public function order()
    {
        return $this->belongsTo(Orders::class, 'order_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }


}