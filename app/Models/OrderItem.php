<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function review()
    {
        return $this->hasOne(Review::class, 'product_id', 'product_id')
                    ->whereColumn('order_id', 'order_id');
    }

    public function return()
    {
        return $this->hasOne(ProductReturn::class, 'product_id', 'product_id')
                    ->whereColumn('order_id', 'order_id');
    }
}
