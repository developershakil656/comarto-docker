<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReviewGallery extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'url',
        'business_review_id',
        'status',
    ];

    
    public function business_review()
    {
        return $this->belongsTo(BusinessReview::class, 'business_review_id', 'id');
    }
}
