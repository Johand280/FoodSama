@use('Illuminate\Support\Number')

<x-app-layout title="Comprobante {{ $receipt->number }}">
    <div class="no-print mb-6 flex flex-wrap items-center justify-between gap-3">
        <a href="{{ route('receipts.index') }}" class="text-sm font-semibold text-brand-700 hover:underline">← Volver a comprobantes</a>
        <button type="button" onclick="window.print()" class="rounded-xl bg-neutral-900 px-4 py-2.5 text-sm font-semibold text-white">Imprimir</button>
    </div>

    <article class="print-area mx-auto max-w-2xl rounded-2xl border border-neutral-200 bg-white p-8 shadow-sm">
        <header class="border-b border-neutral-200 pb-6">
            <p class="text-xs font-semibold uppercase tracking-widest text-brand-700">Comprobante interno de venta</p>
            <h1 class="mt-2 text-2xl font-bold text-neutral-950">{{ $business->name }}</h1>
            <p class="mt-1 text-sm text-neutral-600">{{ $business->phone }}</p>
            @if ($business->address)
                <p class="mt-1 text-sm text-neutral-600">{{ $business->address }}</p>
            @endif
            <div class="mt-5 flex flex-wrap justify-between gap-2 text-sm">
                <span class="font-semibold">{{ $receipt->number }}</span>
                <time datetime="{{ $receipt->issued_at->toIso8601String() }}">{{ $receipt->issued_at->format('d/m/Y H:i') }}</time>
            </div>
        </header>

        <section class="border-b border-neutral-200 py-5 text-sm">
            <p><span class="text-neutral-500">Pedido:</span> {{ $order->order_number }}</p>
            <p class="mt-1"><span class="text-neutral-500">Cliente:</span> {{ $receipt->customer_name }}</p>
            @if ($receipt->customer_phone)
                <p class="mt-1"><span class="text-neutral-500">Teléfono:</span> {{ $receipt->customer_phone }}</p>
            @endif
        </section>

        <table class="w-full text-sm">
            <thead class="text-left text-xs uppercase tracking-wide text-neutral-500">
                <tr><th class="py-3">Producto</th><th class="py-3 text-center">Cant.</th><th class="py-3 text-right">Total</th></tr>
            </thead>
            <tbody class="divide-y divide-neutral-100">
                @foreach ($order->items as $item)
                    <tr>
                        <td class="py-3">{{ $item->name }}<span class="block text-xs text-neutral-500">{{ Number::currency($item->unit_price, $currency, locale: 'es-CO') }} c/u</span></td>
                        <td class="py-3 text-center">{{ $item->quantity }}</td>
                        <td class="py-3 text-right">{{ Number::currency($item->lineTotal(), $currency, locale: 'es-CO') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-4 flex justify-between border-t border-neutral-200 pt-4 text-lg font-bold">
            <span>Total pagado</span>
            <span>{{ Number::currency($receipt->total, $currency, locale: 'es-CO') }}</span>
        </div>
        <p class="mt-6 text-center text-xs text-neutral-500">Documento interno de venta; no corresponde a facturación electrónica fiscal.</p>
    </article>
</x-app-layout>
