<?php

namespace App\Http\Controllers;

use App\Models\Loss;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user   = Auth::user();
        $losses = $user->losses()->with('product')->orderByDesc('created_at')->get();
      

        // Stats
        $totalLosses      = $losses->count();
        $totalLossValue   = $losses->sum(fn($l) => $l->totalValue());
        $thisMonthLosses  = $losses->filter(fn($l) => $l->loss_date->isCurrentMonth())->count();
        $thisMonthValue   = $losses->filter(fn($l) => $l->loss_date->isCurrentMonth())->sum(fn($l) => $l->totalValue());

        // Top products by loss quantity
        $topProducts = $user->losses()
            ->with('product')
            ->selectRaw('product_id, SUM(quantity) as total_qty, SUM(quantity * purchase_price) as total_value')
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        // Losses by reason
        $byReason = $user->losses()
            ->selectRaw('reason, COUNT(*) as count, SUM(quantity * purchase_price) as value')
            ->groupBy('reason')
            ->orderByDesc('count')
            ->get();

        // Recent losses
        $recentLosses = $user->losses()
            ->with('product')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        // Monthly trend (last 6 months)
        $monthlyTrend = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthlyTrend[] = [
                'label' => $month->translatedFormat('M Y'),
                'count' => $user->losses()
                    ->whereYear('loss_date', $month->year)
                    ->whereMonth('loss_date', $month->month)
                    ->count(),
                'value' => $user->losses()
                    ->whereYear('loss_date', $month->year)
                    ->whereMonth('loss_date', $month->month)
                    ->selectRaw('COALESCE(SUM(quantity * purchase_price), 0) as v')
                    ->value('v') ?? 0,
            ];
        }

        return view('dashboard', compact(
            'losses', 'totalLosses', 'totalLossValue',
            'thisMonthLosses', 'thisMonthValue',
            'topProducts', 'byReason', 'recentLosses', 'monthlyTrend'
        ));
    }
}
