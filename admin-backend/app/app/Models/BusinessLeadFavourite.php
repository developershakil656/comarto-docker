<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessLeadFavourite extends Model
{
    protected $fillable = ['business_id', 'lead_id'];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }
}
