@use('Illuminate\Support\Number')

<x-app-layout title="Pedidos">
    @php $currency = auth()->user()->business->currency; @endphp

    <x-page-header title="Pedidos" subtitle="Consulta y atiende los pedidos de tu negocio.">
        <a href="{{ route('orders.create') }}" class="rounded-xl bg-brand-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-brand-700">Registrar pedido</a>
    </x-page-header>

    <form method="GET" action="{{ route('orders.index') }}" class="mb-5 flex flex-col gap-3 rounded-2xl border border-neutral-200 bg-white p-4 sm:flex-row">
        <input type="search" name="search" value="{{ $search }}" placeholder="Código o nombre del cliente" class="min-w-0 flex-1 rounded-xl border border-neutral-300 px-4 py-2.5 text-sm">
        <select name="status" class="rounded-xl border border-neutral-300 px-4 py-2.5 text-sm">
            <option value="">Todos los estados</option>
            <option value="active" @selected($status === 'active')>Activos</option>
            @foreach ($statuses as $statusOption)
                <option value="{{ $statusOption['value'] }}" @selected($status === $statusOption['value'])>{{ $statusOption['label'] }}</option>
            @endforeach
        </select>
        <button class="rounded-xl bg-neutral-900 px-5 py-2.5 text-sm font-semibold text-white">Filtrar</button>
    </form>

    <div class="overflow-hidden rounded-2xl border border-neutral-200 bg-white">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[700px] text-left text-sm">
                <thead class="bg-neutral-50 text-xs uppercase tracking-wide text-neutral-500">
                    <tr>
                        <th class="px-5 py-3">Pedido</th>
                        <th class="px-5 py-3">Cliente</th>
                        <th class="px-5 py-3">Origen</th>
                        <th class="px-5 py-3">Estado / pago</th>
                        <th class="px-5 py-3 text-right">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100">
                    @forelse ($orders as $order)
                        <tr>
                            <td class="px-5 py-4">
                                <a href="{{ route('orders.show', $order) }}" class="font-semibold text-brand-700 hover:underline">{{ $order->order_number }}</a>
                                <p class="mt-1 text-xs text-neutral-500">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                            </td>
                            <td class="px-5 py-4">
                                <p class="font-medium text-neutral-900">{{ $order->customer_name }}</p>
                                <p class="mt-1 text-xs text-neutral-500">{{ $order->customer_phone }}</p>
                            </td>
                            <td class="px-5 py-4">{{ $order->origin->label() }}</td>
                            <td class="px-5 py-4">
                                <x-status-badge :status="$order->status" />
                                <p class="mt-1 text-xs {{ $order->paid_at ? 'text-emerald-700' : 'text-amber-700' }}">{{ $order->paid_at ? 'Pagado' : 'Pago pendiente' }}</p>
                            </td>
                            <td class="px-5 py-4 text-right font-semibold">{{ Number::currency($order->total, $currency, locale: 'es-CO') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-5 py-12 text-center text-neutral-500">No hay pedidos para mostrar.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-neutral-100 px-5 py-4">{{ $orders->links() }}</div>
    </div>
</x-app-layout>
