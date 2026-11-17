<?php

namespace App\Actions;

use App\Models\Business;
use App\Support\Money;
use Carbon\CarbonImmutable;

class BuildProfitReport
{
    /**
     * @return array{
     *     sales: string,
     *     costs: string,
     *     gross_profit: string,
     *     expenses: string,
     *     operating_result: string,
     *     margin: float|null,
     *     orders_count: int,
     *     expenses_count: int,
     *     missing_cost_lines: int,
     *     is_incomplete: bool
     * }
     */
    public function handle(Business $business, CarbonImmutable $startDate, CarbonImmutable $endDate): array
    {
        $orders = $business->orders()
            ->whereNotNull('completed_at')
            ->whereBetween('completed_at', [$startDate, $endDate])
            ->with('items')
            ->get();

        $salesCents = 0;
        $costCents = 0;
        $missingCostLines = 0;

        foreach ($orders as $order) {
            $salesCents += Money::toMinorUnits($order->total);

            foreach ($order->items as $item) {
                if ($item->unit_cost === null) {
                    $missingCostLines++;

                    continue;
                }

                $costCents += Money::toMinorUnits($item->unit_cost) * $item->quantity;
            }
        }

        $expenses = $business->expenses()
            ->whereBetween('expense_date', [
                $startDate->startOfDay()->toDateTimeString(),
                $endDate->endOfDay()->toDateTimeString(),
            ])
            ->get(['amount']);
        $expensesCents = $expenses->sum(fn ($expense): int => Money::toMinorUnits($expense->amount));
        $grossProfitCents = $salesCents - $costCents;
        $operatingResultCents = $grossProfitCents - $expensesCents;

        return [
            'sales' => Money::fromMinorUnits($salesCents),
            'costs' => Money::fromMinorUnits($costCents),
            'gross_profit' => Money::fromMinorUnits($grossProfitCents),
            'expenses' => Money::fromMinorUnits($expensesCents),
            'operating_result' => Money::fromMinorUnits($operatingResultCents),
            'margin' => $salesCents > 0 ? round(($operatingResultCents / $salesCents) * 100, 2) : null,
            'orders_count' => $orders->count(),
            'expenses_count' => $expenses->count(),
            'missing_cost_lines' => $missingCostLines,
            'is_incomplete' => $missingCostLines > 0,
        ];
    }
}
