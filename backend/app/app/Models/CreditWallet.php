<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CreditWallet extends Model
{
    protected $fillable = ['business_id','paid_credits'];
    public function business() { return $this->belongsTo(Business::class); }
}
