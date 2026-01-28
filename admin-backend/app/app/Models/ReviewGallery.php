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
}
