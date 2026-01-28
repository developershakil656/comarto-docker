<?php

namespace App\Services;


use App\Models\CreditWallet;
use Illuminate\Support\Facades\DB;


class LeadCreditService
{
    /** Add purchased credits to wallet */
    public function addPaidCredits(int $businessId, int $credits, int $durationMonths = 3): void
    {
        DB::transaction(function () use ($businessId, $credits, $durationMonths) {
            $wallet = CreditWallet::lockForUpdate()
                ->firstOrCreate(['business_id' => $businessId]);

            $newExpireDate = now()->addMonths($durationMonths);

            // If no expire_date OR expire_date < newExpireDate → update
            if (!$wallet->expire_date || $wallet->expire_date < $newExpireDate) {
                $wallet->expire_date = $newExpireDate;
            }

            $wallet->increment('paid_credits', $credits);
            $wallet->save();
        });
    }
}
