@use('Illuminate\Support\Number')

<x-app-layout title="Pedido {{ $order->order_number }}">
    @php
        $currency = auth()->user()->business->currency;
        $nextStatuses = match ($order->status) {
            \App\OrderStatus::Pending => [\App\OrderStatus::Preparing, \App\OrderStatus::Cancelled],
            \App\OrderStatus::Preparing => [\App\OrderStatus::Ready, \App\OrderStatus::Cancelled],
            \App\OrderStatus::Ready => [\App\OrderStatus::Delivered, \App\OrderStatus::Cancelled],
            default => [],
        };
    @endphp

    <x-page-header :title="'Pedido '.$order->order_number" subtitle="Registrado {{ $order->created_at->format('d/m/Y H:i') }}">
        <x-status-badge :status="$order->status" />
    </x-page-header>

    <div class="grid gap-6 lg:grid-cols-[1.4fr_1fr]">
        <section class="space-y-6">
            <div class="rounded-2xl border border-neutral-200 bg-white p-5">
                <h2 class="font-semibold text-neutral-950">Detalle del pedido</h2>
                <div class="mt-4 divide-y divide-neutral-100">
                    @foreach ($order->items as $item)
                        <div class="flex items-start justify-between gap-4 py-4">
                            <div>
                                <p class="font-medium">{{ $item->quantity }} × {{ $item->name }}</p>
                                <p class="mt-1 text-sm text-neutral-500">{{ Number::currency($item->unit_price, $currency, locale: 'es-CO') }} cada uno</p>
                                @if ($isOwner)
                                    <p class="mt-1 text-xs text-neutral-500">Costo histórico: {{ $item->unit_cost === null ? 'Sin registrar' : Number::currency($item->unit_cost, $currency, locale: 'es-CO') }}</p>
                                @endif
                            </div>
                            <p class="shrink-0 font-semibold">{{ Number::currency($item->lineTotal(), $currency, locale: 'es-CO') }}</p>
                        </div>
                    @endforeach
                </div>
                <div class="flex justify-between border-t border-neutral-200 pt-4 text-lg font-bold">
                    <span>Total</span>
                    <span>{{ Number::currency($order->total, $currency, locale: 'es-CO') }}</span>
                </div>
            </div>

            <div class="rounded-2xl border border-neutral-200 bg-white p-5">
                <h2 class="font-semibold text-neutral-950">Historial</h2>
                <ol class="mt-4 space-y-4">
                    @foreach ($order->events as $event)
                        <li class="flex gap-3 text-sm">
                            <span class="mt-1.5 size-2 shrink-0 rounded-full bg-brand-500"></span>
                            <span>
                                @if ($event->event_type === 'payment')
                                    Pago confirmado
                                @else
                                    Estado: {{ $event->from_value ? \App\OrderStatus::tryFrom($event->from_value)?->label() : 'Creado' }}
                                    → {{ \App\OrderStatus::tryFrom($event->to_value)?->label() ?? $event->to_value }}
                                @endif
                                <span class="text-neutral-500">· {{ $event->user?->name ?? 'Cliente web' }} · {{ $event->occurred_at->format('d/m/Y H:i') }}</span>
                                @if ($event->note)
                                    <span class="mt-1 block text-neutral-600">{{ $event->note }}</span>
                                @endif
                            </span>
                        </li>
                    @endforeach
                </ol>
            </div>
        </section>

        <aside class="space-y-6">
            <div class="rounded-2xl border border-neutral-200 bg-white p-5">
                <h2 class="font-semibold text-neutral-950">Cliente y entrega</h2>
                <dl class="mt-4 space-y-3 text-sm">
                    <div><dt class="text-neutral-500">Nombre</dt><dd class="font-medium">{{ $order->customer_name }}</dd></div>
                    <div><dt class="text-neutral-500">Teléfono</dt><dd class="font-medium">{{ $order->customer_phone }}</dd></div>
                    <div><dt class="text-neutral-500">Origen</dt><dd class="font-medium">{{ $order->origin->label() }}</dd></div>
                    <div><dt class="text-neutral-500">Modalidad</dt><dd class="font-medium">{{ $order->channel->label() }}</dd></div>
                    @if ($order->delivery_address)
                        <div><dt class="text-neutral-500">Dirección</dt><dd class="font-medium">{{ $order->delivery_address }}</dd></div>
                    @endif
                    @if ($order->notes)
                        <div><dt class="text-neutral-500">Observaciones</dt><dd class="font-medium">{{ $order->notes }}</dd></div>
                    @endif
                    @if ($order->cancellation_reason)
                        <div><dt class="text-neutral-500">Motivo de cancelación</dt><dd class="font-medium">{{ $order->cancellation_reason }}</dd></div>
                    @endif
                </dl>
            </div>

            <div class="rounded-2xl border border-neutral-200 bg-white p-5">
                <h2 class="font-semibold text-neutral-950">Atención y pago</h2>
                <p class="mt-3 text-sm">Pago: <span class="font-semibold {{ $order->paid_at ? 'text-emerald-700' : 'text-amber-700' }}">{{ $order->paid_at ? 'Confirmado' : 'Pendiente' }}</span></p>
                @if ($order->delivered_at)
                    <p class="mt-1 text-sm text-neutral-500">Entregado {{ $order->delivered_at->format('d/m/Y H:i') }}</p>
                @endif

                @if (! $order->paid_at && $order->status !== \App\OrderStatus::Cancelled)
                    <form method="POST" action="{{ route('orders.payment', $order) }}" class="mt-4">
                        @csrf
                        @method('PATCH')
                        <button class="w-full rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-2.5 text-sm font-semibold text-emerald-800 hover:bg-emerald-100">Confirmar pago manualmente</button>
                    </form>
                @endif

                @if ($nextStatuses !== [])
                    <form method="POST" action="{{ route('orders.status', $order) }}" class="mt-4 space-y-3 border-t border-neutral-100 pt-4">
                        @csrf
                        @method('PATCH')
                        <label for="status" class="block text-sm font-semibold">Cambiar estado</label>
                        <select id="status" name="status" required class="w-full rounded-xl border border-neutral-300 px-3 py-2.5 text-sm">
                            @foreach ($nextStatuses as $nextStatus)
                                <option value="{{ $nextStatus->value }}">{{ $nextStatus->label() }}</option>
                            @endforeach
                        </select>
                        <label for="cancellation_reason" class="block text-sm font-medium text-neutral-600">Motivo si cancelas</label>
                        <textarea id="cancellation_reason" name="cancellation_reason" rows="2" maxlength="500" class="w-full rounded-xl border border-neutral-300 px-3 py-2.5 text-sm"></textarea>
                        @error('status')<p class="text-sm text-rose-700">{{ $message }}</p>@enderror
                        @error('cancellation_reason')<p class="text-sm text-rose-700">{{ $message }}</p>@enderror
                        <button class="w-full rounded-xl bg-brand-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-brand-700">Guardar estado</button>
                    </form>
                @endif

                @if ($order->receipt)
                    <a href="{{ route('receipts.show', $order->receipt) }}" class="mt-4 block rounded-xl border border-neutral-200 px-4 py-2.5 text-center text-sm font-semibold text-brand-700 hover:bg-neutral-50">Ver comprobante {{ $order->receipt->number }}</a>
                @elseif ($order->isCompleted())
                    <p class="mt-3 text-sm text-amber-700">El pedido está entregado y pagado; el comprobante se está preparando.</p>
                @endif
            </div>
        </aside>
    </div>
</x-app-layout>
