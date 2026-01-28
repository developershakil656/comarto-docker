<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeadCreditPackage extends Model
{
    protected $fillable = [
        'name', 'credits', 'price', 'currency', 'duration_months', 'is_active'
    ];
}
