<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Exportable;

class Cart extends Model
{
    use HasFactory;
    use Exportable;

    // Define the fillable fields
    protected $fillable = ['user_id'];

    // Define the relationship with CartItem
    public function items()
    {
        return $this->hasMany(CartItem::class);
    }

    // Define the relationship with User (if applicable)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
