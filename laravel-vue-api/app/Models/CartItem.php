<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    use HasFactory;

    // Define the fillable fields
    protected $fillable = ['cart_id', 'product_id', 'quantity'];

    // Define the relationship with Cart
    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    // Define the relationship with Product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
