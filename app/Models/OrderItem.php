<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'food_item_id',
        'item_name',
        'category_name',
        'portion',
        'quantity',
        'unit_price',
        'total_price',
        'notes',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function foodItem()
    {
        return $this->belongsTo(FoodItem::class);
    }
}
