<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeadCreditPurchase extends Model
{
    protected $fillable = ['business_id', 'package_id', 'credits', 'amount', 'payment_method', 'transaction_id', 'status', 'expire_date'];
    public function business() { return $this->belongsTo(Business::class); }
    public function package()
    {
        return $this->belongsTo(LeadCreditPackage::class);
    }
}
