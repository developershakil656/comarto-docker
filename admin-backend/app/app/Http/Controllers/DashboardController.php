<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AccountVerification;
use App\Models\User;
use App\Models\Business;
use App\Models\Conversation;
use App\Models\Lead;
use App\Models\LeadCreditPurchase;
use App\Models\Product;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function dashboard_summary()
    {
        $userCount = User::where('status', 'active')->count();
        $businessCount = Business::where('status', 'active')->count();
        $productCount = Product::where('status', 'active')->count();
        $leadCount = Lead::where('status', 'open')->count();
        $conversationCount = Conversation::count();
        $verificationPendingCount = AccountVerification::where('status', 'in review')->count();
        $creditPurchacePendingCount = LeadCreditPurchase::where('status', 'pending')->count();

        $data = [
            'userCount' => $userCount,
            'businessCount' => $businessCount,
            'productCount' => $productCount,
            'conversationCount' => $conversationCount,
            'leadCount' => $leadCount,
            'verificationPendingCount' => $verificationPendingCount,
            'creditPurchacePendingCount' => $creditPurchacePendingCount,
        ];

        return success_response('all summary', $data);
    }

    public function todayStats()
    {
        $today = now()->startOfDay();

        return success_response('today stats', [
            'user'         => User::where('created_at', '>=', $today)->count(),
            'business'     => Business::where('created_at', '>=', $today)->count(),
            'product'      => Product::where('created_at', '>=', $today)->count(),
            'lead'         => Lead::where('created_at', '>=', $today)->count(),
            'conversation' => Conversation::where('created_at', '>=', $today)->count(),
        ]);
    }

    public function weeklyGraph()
    {
        // Week starts Saturday → ends Friday
        $startDate = now()->previous(Carbon::SATURDAY)->startOfDay();
        $endDate   = $startDate->copy()->addDays(6)->endOfDay(); // Friday

        // Fixed labels (Sat → Fri)
        $labels = [
            'Sat',
            'Sun',
            'Mon',
            'Tue',
            'Wed',
            'Thu',
            'Fri'
        ];

        // Generate an array of dates for Sat → Fri
        $weekDates = [];
        for ($i = 0; $i < 7; $i++) {
            $weekDates[] = $startDate->copy()->addDays($i)->toDateString();
        }

        // Helper to generate graph data + zero fill
        $makeWeeklyGraph = function ($model) use ($startDate, $endDate, $weekDates) {
            // Fetch grouped data from DB
            $data = $model::selectRaw('DATE(created_at) as date, COUNT(*) as total')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->groupBy('date')
                ->pluck('total', 'date') // returns ['2025-01-10' => 5]
                ->toArray();

            // Zero-fill for missing dates
            $final = [];
            foreach ($weekDates as $date) {
                $final[] = $data[$date] ?? 0;
            }

            return $final;
        };

        return success_response('weekly graph', [
            'labels'       => ['Sat', 'Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri'],
            'start'        => $startDate->toDateString(),
            'end'          => $endDate->toDateString(),

            // Graph data mapped exactly to the labels
            'user'         => $makeWeeklyGraph(User::class),
            'business'     => $makeWeeklyGraph(Business::class),
            'product'      => $makeWeeklyGraph(Product::class),
            'lead'         => $makeWeeklyGraph(Lead::class),
            'conversation' => $makeWeeklyGraph(Conversation::class),
        ]);
    }
}
