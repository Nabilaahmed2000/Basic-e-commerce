<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $fillable=[
        'user_id',
        'address_id',
        'total_price',
        'status',
    ];

    //order has many order items
    public function orderItems(){
        return $this->hasMany(OrderItem::class);
    }
}
