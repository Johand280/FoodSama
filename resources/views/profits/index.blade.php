@use('Illuminate\Support\Number')

<x-app-layout title="Rentabilidad">
    @php $currency = auth()->user()->business->currency; @endphp

    <x-page-header title="Rentabilidad estimada" subtitle="Ventas por fecha de entrega y confirmación de pago, menos costos y gastos registrados.">
        <a href="{{ route('profits.export', ['start_date' => $startDate->toDateString(), 'end_date' => $endDate->toDateString()]) }}" class="rounded-xl border border-neutral-300 bg-white px-4 py-2.5 text-sm font-semibold text-neutral-700 hover:bg-neutral-50">Descargar CSV</a>
    </x-page-header>

    <form method="GET" action="{{ route('profits.index') }}" class="mb-6 flex flex-col gap-3 rounded-2xl border border-neutral-200 bg-white p-4 sm:flex-row sm:items-end">
        <div>
            <label for="start_date" class="mb-1 block text-xs font-semibold text-neutral-600">Desde</label>
            <input id="start_date" type="date" name="start_date" value="{{ $startDate->toDateString() }}" required class="rounded-xl border border-neutral-300 px-3 py-2.5 text-sm">
        </div>
        <div>
            <label for="end_date" class="mb-1 block text-xs font-semibold text-neutral-600">Hasta</label>
            <input id="end_date" type="date" name="end_date" value="{{ $endDate->toDateString() }}" required class="rounded-xl border border-neutral-300 px-3 py-2.5 text-sm">
        </div>
        <button class="rounded-xl bg-neutral-900 px-5 py-2.5 text-sm font-semibold text-white">Consultar</button>
    </form>

    @if ($report['is_incomplete'])
        <div class="mb-5 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
            El resultado está incompleto: hay {{ $report['missing_cost_lines'] }} línea(s) vendida(s) sin costo histórico registrado.
        </div>
    @endif

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
        <article class="rounded-2xl border border-neutral-200 bg-white p-5">
            <p class="text-sm text-neutral-500">Ventas ({{ $report['orders_count'] }} pedidos)</p>
            <p class="mt-3 text-2xl font-bold">{{ Number::currency($report['sales'], $currency, locale: 'es-CO') }}</p>
        </article>
        <article class="rounded-2xl border border-neutral-200 bg-white p-5">
            <p class="text-sm text-neutral-500">Costos de productos</p>
            <p class="mt-3 text-2xl font-bold">{{ Number::currency($report['costs'], $currency, locale: 'es-CO') }}</p>
        </article>
        <article class="rounded-2xl border border-neutral-200 bg-white p-5">
            <p class="text-sm text-neutral-500">Utilidad bruta</p>
            <p class="mt-3 text-2xl font-bold">{{ Number::currency($report['gross_profit'], $currency, locale: 'es-CO') }}</p>
        </article>
        <article class="rounded-2xl border border-neutral-200 bg-white p-5">
            <p class="text-sm text-neutral-500">Gastos operativos ({{ $report['expenses_count'] }})</p>
            <p class="mt-3 text-2xl font-bold">{{ Number::currency($report['expenses'], $currency, locale: 'es-CO') }}</p>
            <a href="{{ route('expenses.index') }}" class="mt-2 inline-block text-sm font-semibold text-brand-700 hover:underline">Registrar gastos</a>
        </article>
        <article class="rounded-2xl border border-brand-200 bg-brand-50 p-5">
            <p class="text-sm text-brand-800">Resultado operativo estimado</p>
            <p class="mt-3 text-2xl font-bold text-brand-900">{{ Number::currency($report['operating_result'], $currency, locale: 'es-CO') }}</p>
        </article>
        <article class="rounded-2xl border border-neutral-200 bg-white p-5">
            <p class="text-sm text-neutral-500">Margen sobre ventas</p>
            <p class="mt-3 text-2xl font-bold">{{ $report['margin'] === null ? 'No calculable' : number_format($report['margin'], 2, ',', '.').'%' }}</p>
        </article>
    </div>

    <p class="mt-5 text-xs text-neutral-500">Período: {{ $startDate->format('d/m/Y') }} al {{ $endDate->format('d/m/Y') }}. Los valores son estimados con los costos y gastos registrados.</p>
</x-app-layout>
