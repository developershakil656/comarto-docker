<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductGallery extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'url',
        'product_id',
        'status',
        'serial'
    ];

    
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
}
