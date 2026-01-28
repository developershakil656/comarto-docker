<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessReview extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'rating',
        'message',
        'status',
        'user_id',
        'business_id',
        'parent_id'
    ];

    protected $casts = [
        'rating' => 'float',
    ];

    public function business()
    {
        return $this->belongsTo(Business::class, 'business_id', 'id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    // Get the children of a category
    public function replies()
    {
        return $this->hasMany(BusinessReview::class, 'parent_id');
    }

    public function gallery()
    {
        return $this->hasMany(ReviewGallery::class, 'business_review_id');
    }
}
