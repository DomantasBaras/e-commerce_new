<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Exportable;

class Log extends Model
{
    use HasFactory;
    use Exportable;

    protected $fillable = ['action', 'details', 'user_id','model'];
}
