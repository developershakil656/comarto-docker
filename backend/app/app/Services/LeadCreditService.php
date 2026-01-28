<?php

namespace App\Services;


use App\Models\CreditWallet;
use App\Models\CreditCounter;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;


class LeadCreditService
{
    const MONTHLY_FREE_LIMIT = 10;


    /** Get current balances */
    public function balances(int $businessId): array
    {
        $month = now()->month;
        $year = now()->year;
        $wallet = CreditWallet::firstOrCreate(['business_id' => $businessId]);
        $counter = CreditCounter::firstOrCreate(
            ['business_id' => $businessId, 'month' => $month, 'year' => $year],
            ['free_used' => 0]
        );
        $free_remaining = max(0, self::MONTHLY_FREE_LIMIT - $counter->free_used);
        return [
            'free_limit' => self::MONTHLY_FREE_LIMIT,
            'free_used' => (int)$counter->free_used,
            'free_remaining' => (int)$free_remaining,
            // 'month' => $month,
            // 'year' => $year,
            'paid_credits' => (int)$wallet->paid_credits,
            'expire_date' => $wallet->expire_date?Carbon::parse($wallet->expire_date)->format('d M, Y'):'N/A',
        ];
    }


    /** Consume exactly 1 credit; prefer free, fallback to paid. Returns 'free'|'paid'. */
    public function consumeOne(int $businessId): string
    {
        return DB::transaction(function () use ($businessId) {
            $month = now()->month;
            $year = now()->year;

            // Lock and fetch wallet
            $wallet = CreditWallet::lockForUpdate()->firstOrCreate(
                ['business_id' => $businessId]
            );

            // Lock and fetch counter
            $counter = CreditCounter::lockForUpdate()->firstOrCreate(
                ['business_id' => $businessId, 'month' => $month, 'year' => $year],
                ['free_used' => 0]
            );

            // First use free credits
            if ($counter->free_used < self::MONTHLY_FREE_LIMIT) {
                $counter->increment('free_used');
                return 'free';
            }

            // Check expire_date before using paid credits
            if (
                $wallet->paid_credits > 0 &&
                (! $wallet->expire_date || $wallet->expire_date->endOfDay()->isFuture())
            ) {
                $wallet->decrement('paid_credits');
                return 'paid';
            }

            throw new \RuntimeException('NO_CREDITS');
        });
    }
}
