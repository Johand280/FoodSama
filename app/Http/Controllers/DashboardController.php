<?php

namespace App\Http\Controllers;

use App\Actions\BuildProfitReport;
use App\Models\Order;
use App\OrderStatus;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Show the business overview.
     */
    public function index(Request $request, BuildProfitReport $buildProfitReport): View
    {
        $business = $request->user()->business;
        $isOwner = $request->user()->isOwner();
        $monthStart = CarbonImmutable::now()->startOfMonth();
        $monthEnd = CarbonImmutable::now()->endOfDay();

        $recentOrders = $business->orders()
            ->with('items')
            ->latest()
            ->limit(5)
            ->get();

        $activeOrders = $business->orders()
            ->where(function ($query): void {
                $query->whereIn('status', [OrderStatus::Pending, OrderStatus::Preparing, OrderStatus::Ready])
                    ->orWhere(function ($query): void {
                        $query->where('status', OrderStatus::Delivered)->whereNull('paid_at');
                    });
            })
            ->count();

        $monthReport = $isOwner
            ? $buildProfitReport->handle($business, $monthStart, $monthEnd)
            : null;
        $weeklyRevenue = $isOwner ? $this->weeklyRevenue($business->orders(), 7) : [];

        return view('dashboard', [
            'business' => $business,
            'isOwner' => $isOwner,
            'monthRevenue' => $monthReport['sales'] ?? null,
            'monthProfit' => $monthReport['operating_result'] ?? null,
            'monthOrders' => $monthReport['orders_count'] ?? null,
            'activeOrders' => $activeOrders,
            'availableItems' => $business->menuItems()->where('is_available', true)->count(),
            'recentOrders' => $recentOrders,
            'weeklyRevenue' => $weeklyRevenue,
        ]);
    }

    /**
     * Build a simple day-by-day revenue series for the chart.
     *
     * @param  HasMany<Order>  $orders
     * @return array<int, array{label: string, date: string, total: float}>
     */
    private function weeklyRevenue(HasMany $orders, int $days): array
    {
        $from = now()->startOfDay()->subDays($days - 1);

        $totals = $orders
            ->whereNotNull('completed_at')
            ->where('completed_at', '>=', $from)
            ->get(['completed_at', 'total'])
            ->groupBy(fn (Order $order): string => $order->completed_at->toDateString())
            ->map(fn ($group): float => (float) $group->sum('total'));

        $series = [];

        for ($day = $days - 1; $day >= 0; $day--) {
            $date = now()->startOfDay()->subDays($day);
            $key = $date->toDateString();

            $series[] = [
                'label' => $date->shortDayName,
                'date' => $key,
                'total' => round((float) ($totals->get($key) ?? 0), 2),
            ];
        }

        return $series;
    }
}
