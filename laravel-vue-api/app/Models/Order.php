<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Exportable;

class Order extends Model
{
    use HasFactory;
    use Exportable;

    protected $fillable = [
        'id',
        'user_id',
        'total',
        'status',
        'address', // Added field for shipping address
        'payment_method', // e.g., Credit Card, PayPal, etc.
        'tracking_number', // Optional field for shipment tracking
        'placed_at', // Timestamp when the order was placed
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
