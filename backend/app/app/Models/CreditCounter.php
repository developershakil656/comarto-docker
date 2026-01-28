<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CreditCounter extends Model
{
    protected $fillable = ['business_id','month','year','free_used'];
    public function business() { return $this->belongsTo(Business::class); }
}
