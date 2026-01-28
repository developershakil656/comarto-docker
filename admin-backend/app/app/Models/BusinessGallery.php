<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessGallery extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'url',
        'business_id',
        'status',
        'serial'
    ];

    
    public function business()
    {
        return $this->belongsTo(Business::class, 'business_id', 'id');
    }

}
