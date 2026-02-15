<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class BusinessDetail extends Model
{
     /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'established',
        'number_of_employee',
        'about',
        'direction',
        'payment_type',
        'tin',
        'website',
        'facebook',
        'video_url',
        'timing',
        'business_id'
    ];

    protected function timing(): Attribute{
        return Attribute::make(
            get: fn ($value) => json_decode($value,true),
            set: fn ($value) => json_encode($value),
        );
    }
}
