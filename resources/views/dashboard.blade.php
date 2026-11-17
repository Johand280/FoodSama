@use('Illuminate\Support\Number')
@use('App\OrderStatus')

<x-app-layout :title="'Resumen'">
    @php
        $currency = auth()->user()->business->currency;
        $chartMax = max(1, (float) collect($weeklyRevenue)->max('total'));
    @endphp

    <x-page-header title="Hola, {{ auth()->user()->name }}" subtitle="Así va {{ $business->name }} hoy, {{ now()->translatedFormat('l d \d\e F') }}.">
        <a href="{{ route('orders.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-brand-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm shadow-brand-600/20 hover:bg-brand-700">
            <x-icon name="plus" class="size-4" />
            Registrar pedido
        </a>
        @if ($isOwner)
            <a href="{{ route('menu.create') }}" class="inline-flex items-center gap-2 rounded-xl border border-neutral-200 bg-white px-4 py-2.5 text-sm font-semibold text-neutral-800 shadow-sm hover:border-neutral-300">
                <x-icon name="menu" class="size-4" />
                Agregar producto
            </a>
        @endif
    </x-page-header>

    <div class="grid gap-4 sm:grid-cols-2 {{ $isOwner ? 'xl:grid-cols-4' : 'xl:grid-cols-2' }}">
        @if ($isOwner)
        <div class="rounded-2xl border border-neutral-200 bg-white p-5 shadow-sm shadow-neutral-900/[0.02]">
            <div class="flex items-center justify-between">
                <p class="text-sm font-medium text-neutral-500">Ventas del mes</p>
                <span class="flex size-9 items-center justify-center rounded-xl bg-brand-50 text-brand-700"><x-icon name="chart" class="size-4" /></span>
            </div>
            <p class="mt-4 text-2xl font-semibold tracking-tight text-neutral-950">{{ Number::currency($monthRevenue, $currency, locale: 'es-CO') }}</p>
            <p class="mt-1 text-xs text-neutral-400">{{ $monthOrders }} pedidos completados</p>
        </div>

        <div class="rounded-2xl border border-neutral-200 bg-white p-5 shadow-sm shadow-neutral-900/[0.02]">
            <div class="flex items-center justify-between">
                <p class="text-sm font-medium text-neutral-500">Ganancia estimada</p>
                <span class="flex size-9 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700"><x-icon name="sparkles" class="size-4" /></span>
            </div>
            <p class="mt-4 text-2xl font-semibold tracking-tight text-emerald-700">{{ Number::currency($monthProfit, $currency, locale: 'es-CO') }}</p>
            <p class="mt-1 text-xs text-neutral-400">Después de costos y gastos registrados</p>
        </div>
        @endif

        <div class="rounded-2xl border border-neutral-200 bg-white p-5 shadow-sm shadow-neutral-900/[0.02]">
            <div class="flex items-center justify-between">
                <p class="text-sm font-medium text-neutral-500">Pedidos activos</p>
                <span class="flex size-9 items-center justify-center rounded-xl bg-blue-50 text-blue-700"><x-icon name="orders" class="size-4" /></span>
            </div>
            <p class="mt-4 text-2xl font-semibold tracking-tight text-neutral-950">{{ $activeOrders }}</p>
            <p class="mt-1 text-xs text-neutral-400">Incluye pedidos entregados sin pago confirmado</p>
        </div>

        <div class="rounded-2xl border border-neutral-200 bg-white p-5 shadow-sm shadow-neutral-900/[0.02]">
            <div class="flex items-center justify-between">
                <p class="text-sm font-medium text-neutral-500">Productos disponibles</p>
                <span class="flex size-9 items-center justify-center rounded-xl bg-amber-50 text-amber-700"><x-icon name="menu" class="size-4" /></span>
            </div>
            <p class="mt-4 text-2xl font-semibold tracking-tight text-neutral-950">{{ $availableItems }}</p>
            <p class="mt-1 text-xs text-neutral-400">Listos para vender</p>
        </div>
    </div>

    <div class="mt-6 grid gap-6 {{ $isOwner ? 'xl:grid-cols-[1.5fr_1fr]' : '' }}">
        @if ($isOwner)
        <section class="rounded-2xl border border-neutral-200 bg-white p-6 shadow-sm shadow-neutral-900/[0.02]">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="font-semibold text-neutral-950">Ventas de los últimos 7 días</h2>
                    <p class="mt-1 text-xs text-neutral-500">Pedidos completados</p>
                </div>
                <a href="{{ route('profits.index') }}" class="text-sm font-semibold text-brand-700 hover:underline">Ver reporte</a>
            </div>

            <div class="mt-8 flex h-56 items-end gap-2 sm:gap-4">
                @foreach ($weeklyRevenue as $day)
                    <div class="flex h-full flex-1 flex-col items-center justify-end gap-2">
                        <span class="text-[10px] font-medium text-neutral-400">{{ $day['total'] > 0 ? number_format($day['total'], 0) : '' }}</span>
                        <div class="flex w-full flex-1 items-end">
                            <div class="w-full rounded-t-lg bg-brand-500/80 transition-all hover:bg-brand-600" style="height: {{ max(4, ($day['total'] / $chartMax) * 100) }}%" title="{{ $day['date'] }}: {{ Number::currency($day['total'], $currency, locale: 'es-CO') }}"></div>
                        </div>
                        <span class="text-[11px] font-medium text-neutral-500">{{ $day['label'] }}</span>
                    </div>
                @endforeach
            </div>
        </section>
        @endif

        <section class="rounded-2xl border border-neutral-200 bg-white p-6 shadow-sm shadow-neutral-900/[0.02]">
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-neutral-950">Pedidos recientes</h2>
                <a href="{{ route('orders.index') }}" class="text-sm font-semibold text-brand-700 hover:underline">Ver todos</a>
            </div>

            @if ($recentOrders->isEmpty())
                <div class="mt-8 rounded-xl border border-dashed border-neutral-200 p-8 text-center">
                    <p class="text-sm font-medium text-neutral-700">Todavía no hay pedidos</p>
                    <p class="mt-1 text-xs text-neutral-500">Registra tu primer pedido para verlo aquí.</p>
                </div>
            @else
                <div class="mt-5 divide-y divide-neutral-100">
                    @foreach ($recentOrders as $order)
                        <a href="{{ route('orders.show', $order) }}" class="flex items-center justify-between gap-3 py-3.5 transition hover:opacity-75">
                            <div class="min-w-0">
                                <p class="truncate text-sm font-semibold text-neutral-900">{{ $order->customer_name }}</p>
                                <p class="text-xs text-neutral-500">{{ $order->order_number }} · {{ $order->created_at->diffForHumans() }}</p>
                            </div>
                            <div class="shrink-0 text-right">
                                <p class="text-sm font-semibold text-neutral-900">{{ Number::currency($order->total, $currency, locale: 'es-CO') }}</p>
                                <div class="mt-1"><x-status-badge :status="$order->status" /></div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </section>
    </div>
</x-app-layout>
