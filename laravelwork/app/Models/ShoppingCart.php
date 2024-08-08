<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


class ShoppingCart extends Model
{
    use HasFactory;

    protected $table = 'shoppingcart';

    protected $fillable = 
    [
        'user_id',
        'product_id',
        'num',
    ];
    
    
}
