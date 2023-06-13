<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'sku',
        'quantity',
        'price',
        'category_id',
        'user_id',
    ];

    /**
     * Get the user that owns the product.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the category that owns the product.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the photos for the product.
     */
    public function photos()
    {
        return $this->hasMany(ProductPhoto::class);
    }

    /**
     * Get the transactions for the product.
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
