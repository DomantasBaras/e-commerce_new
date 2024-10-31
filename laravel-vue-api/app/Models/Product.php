<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Product extends Model
{
    use HasFactory;
    use Searchable;
    protected $fillable = ['name', 'description', 'price', 'stock', 'image']; // Include 'image'


    // Define which attributes are searchable
    public function toSearchableArray()
    {
        $array = $this->toArray();

        // Customize array
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
        ];
    }
    // public function reviews()
    // {
    //     return $this->hasMany(Review::class);
    // }

    // public function orders()
    // {
    //     return $this->belongsToMany(Order::class, 'order_items');
    // }
}
