<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'price', 'stock', 'image']; // Include 'image'

    // public function reviews()
    // {
    //     return $this->hasMany(Review::class);
    // }

    // public function orders()
    // {
    //     return $this->belongsToMany(Order::class, 'order_items');
    // }
}
