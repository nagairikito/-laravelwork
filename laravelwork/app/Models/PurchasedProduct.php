<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


class PurchasedProduct extends Model
{
    use HasFactory;

    protected $table = 'purchasedproducts';

    protected $fillable = 
    [
        'user_id',
        'product_id',
    ];
    
    
}
