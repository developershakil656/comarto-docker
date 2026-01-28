<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Business extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'slug',
        'business_name',
        'business_profile',
        'number',
        'alternate_number',
        'email',
        'location_id',
        'address',
        'post_code',
        'business_type',
        'status',
        'user_id',
        'category_ids'
    ];

    protected function businessType(): Attribute
    {
        return Attribute::make(
            get: fn($value) => json_decode($value, true),
            set: fn($value) => json_encode($value),
        );
    }
    protected function category_ids(): Attribute
    {
        return Attribute::make(
            get: fn($value) => json_decode($value, true),
            set: fn($value) => json_encode($value),
        );
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id', 'id');
    }

    public function details()
    {
        return $this->hasOne(BusinessDetail::class, 'business_id', 'id');
    }

    /**
     * Get all reviews for the business.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(BusinessReview::class, 'business_id')->whereNull('parent_id');
    }

    /**
     * Get the average rating and total number of reviews for the business.
     */
    public function averageRating()
    {
        return $this->hasOne(BusinessReview::class, 'business_id')
            ->where('parent_id', null)
            ->selectRaw('ROUND(AVG(rating), 1) as rate, COUNT(*) as count')
            ->groupBy('business_id');
    }

    public function gallery()
    {
        return $this->hasMany(BusinessGallery::class, 'business_id')->orderBy('serial', 'asc');
    }

    public function business_leads()
    {
        return $this->hasMany(BusinessLead::class, 'business_id');
    }

    // Business.php
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
