<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


class FavoriteProduct extends Model
{
    use HasFactory;

    protected $table = 'favoriteproducts';

    protected $fillable = 
    [
        'user_id',
        'product_id',
    ];
    
    
}
