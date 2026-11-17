<?php

namespace App\Http\Controllers;

use App\Actions\BuildProfitReport;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use RuntimeException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProfitController extends Controller
{
    public function index(Request $request, BuildProfitReport $buildProfitReport): View
    {
        [$startDate, $endDate] = $this->period($request);

        return view('profits.index', [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'report' => $buildProfitReport->handle($request->user()->business, $startDate, $endDate),
        ]);
    }

    public function export(Request $request, BuildProfitReport $buildProfitReport): StreamedResponse
    {
        [$startDate, $endDate] = $this->period($request);
        $report = $buildProfitReport->handle($request->user()->business, $startDate, $endDate);

        return response()->streamDownload(function () use ($report, $startDate, $endDate): void {
            $stream = fopen('php://output', 'wb');

            if ($stream === false) {
                throw new RuntimeException('No fue posible generar el archivo CSV.');
            }

            $rows = [
                ['Desde', 'Hasta', 'Pedidos', 'Ventas COP', 'Costos COP', 'Utilidad bruta COP', 'Gastos COP', 'Resultado operativo COP', 'Margen (%)', 'Costos incompletos'],
                [
                    $startDate->toDateString(),
                    $endDate->toDateString(),
                    $report['orders_count'],
                    $report['sales'],
                    $report['costs'],
                    $report['gross_profit'],
                    $report['expenses'],
                    $report['operating_result'],
                    $report['margin'] === null ? 'No calculable' : number_format($report['margin'], 2, '.', ''),
                    $report['is_incomplete'] ? 'Sí' : 'No',
                ],
            ];

            foreach ($rows as $row) {
                if (fputcsv($stream, $row, ',', '"', '\\') === false) {
                    fclose($stream);
                    throw new RuntimeException('No fue posible escribir una fila del archivo CSV.');
                }
            }

            fclose($stream);
        }, "food-sama-reporte-{$startDate->toDateString()}-{$endDate->toDateString()}.csv", [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * @return array{CarbonImmutable, CarbonImmutable}
     */
    private function period(Request $request): array
    {
        $validated = $request->validate([
            'start_date' => ['nullable', 'date_format:Y-m-d'],
            'end_date' => ['nullable', 'date_format:Y-m-d'],
        ]);

        $startDate = CarbonImmutable::parse(
            $validated['start_date'] ?? now()->startOfMonth()->toDateString(),
        )->startOfDay();
        $endDate = CarbonImmutable::parse(
            $validated['end_date'] ?? now()->toDateString(),
        )->endOfDay();

        if ($startDate->greaterThan($endDate)) {
            throw ValidationException::withMessages([
                'end_date' => 'La fecha final debe ser igual o posterior a la fecha inicial.',
            ]);
        }

        return [$startDate, $endDate];
    }
}
