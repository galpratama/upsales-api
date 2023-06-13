<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ProductPhoto extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'path'
    ];

    protected $appends = [
        'full_path'
    ];

    /**
     * Get the full path of the file.
     *
     * @return string
     */
    public function getFullPathAttribute(): string
    {
        // Generate a fully qualified URL for the file path on the server.
        return url(Storage::url($this->path));
    }


    /**
     * Get the product that owns the product photo.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
