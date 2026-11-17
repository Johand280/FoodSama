@use('Illuminate\Support\Number')

<x-app-layout title="Comprobantes">
    @php $currency = auth()->user()->business->currency; @endphp

    <x-page-header title="Comprobantes" subtitle="Comprobantes internos de ventas entregadas y pagadas." />

    <div class="overflow-hidden rounded-2xl border border-neutral-200 bg-white">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[650px] text-left text-sm">
                <thead class="bg-neutral-50 text-xs uppercase tracking-wide text-neutral-500">
                    <tr>
                        <th class="px-5 py-3">Comprobante</th>
                        <th class="px-5 py-3">Pedido / cliente</th>
                        <th class="px-5 py-3">Fecha</th>
                        <th class="px-5 py-3 text-right">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100">
                    @forelse ($receipts as $receipt)
                        <tr>
                            <td class="px-5 py-4"><a href="{{ route('receipts.show', $receipt) }}" class="font-semibold text-brand-700 hover:underline">{{ $receipt->number }}</a></td>
                            <td class="px-5 py-4">
                                <a href="{{ route('orders.show', $receipt->order) }}" class="font-medium text-neutral-900 hover:underline">{{ $receipt->order->order_number }}</a>
                                <p class="mt-1 text-xs text-neutral-500">{{ $receipt->customer_name }}</p>
                            </td>
                            <td class="px-5 py-4">{{ $receipt->issued_at->format('d/m/Y H:i') }}</td>
                            <td class="px-5 py-4 text-right font-semibold">{{ Number::currency($receipt->total, $currency, locale: 'es-CO') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-5 py-12 text-center text-neutral-500">Aún no se han generado comprobantes.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-neutral-100 px-5 py-4">{{ $receipts->links() }}</div>
    </div>
</x-app-layout>
