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
            get: fn ($value) => json_decode($value, true),
            set: function ($value) {
                // incoming value may already be a JSON string (from frontend FormData)
                // or an array/collection. We want to store a valid JSON string only once.
                if (is_string($value)) {
                    // if it's a JSON string, make sure it decodes properly
                    $decoded = json_decode($value, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        // store nicely formatted JSON of the decoded value
                        return json_encode($decoded);
                    }
                    // fall back to raw string (should not happen)
                    return $value;
                }

                // otherwise encode arrays/objects normally
                return json_encode($value);
            },
        );
    }
}
