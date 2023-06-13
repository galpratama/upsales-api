<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;


    protected $fillable = [
        'order_id',
        'user_id',
        'product_id',
        'price_per_item',
        'quantity',
        'vat',
        'total',
        'payment_method',
        'payment_url',
        'status',
    ];

    /**
     * Get the user that owns the transaction.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the product that owns the transaction.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
