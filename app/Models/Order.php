<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'order_type',
        'table_no',
        'attendant',
        'order_time',
        'subtotal',
        'tax',
        'total_amount',
        'status',
        'payment_method',
        'cash_amount',
        'online_amount',
        'notes',
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
