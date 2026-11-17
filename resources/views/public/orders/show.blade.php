@use('Illuminate\Support\Number')

<x-guest-layout :title="'Pedido confirmado · '.$business->name">
    <main class="mx-auto flex min-h-screen max-w-2xl items-center px-5 py-12">
        <section class="w-full rounded-3xl border border-neutral-200 bg-white p-7 text-center shadow-sm sm:p-10">
            <span class="mx-auto flex size-14 items-center justify-center rounded-full bg-emerald-100 text-2xl text-emerald-700">✓</span>
            <p class="mt-5 text-sm font-semibold uppercase tracking-widest text-brand-700">{{ $business->name }}</p>
            <h1 class="mt-2 text-2xl font-bold text-neutral-950">Tu pedido quedó registrado</h1>
            <p class="mt-2 text-neutral-600">Código <span class="font-semibold">{{ $order->order_number }}</span>. El negocio debe confirmar y atender el pedido.</p>

            <div class="my-6 rounded-2xl bg-neutral-50 p-5 text-left">
                @foreach ($order->items as $item)
                    <div class="flex justify-between gap-4 py-2 text-sm">
                        <span>{{ $item->quantity }} × {{ $item->name }}</span>
                        <span class="font-medium">{{ Number::currency($item->lineTotal(), $business->currency, locale: 'es-CO') }}</span>
                    </div>
                @endforeach
                <div class="mt-2 flex justify-between border-t border-neutral-200 pt-4 font-bold">
                    <span>Total</span>
                    <span>{{ Number::currency($order->total, $business->currency, locale: 'es-CO') }}</span>
                </div>
            </div>

            <p class="mb-4 text-sm text-neutral-500">El pedido ya está guardado. Puedes enviar el resumen al negocio por WhatsApp.</p>
            <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener noreferrer" class="block rounded-xl bg-emerald-600 px-5 py-3 text-sm font-semibold text-white hover:bg-emerald-700">Enviar resumen por WhatsApp</a>
            <a href="{{ route('public.menu', $business->slug) }}" class="mt-4 inline-block text-sm font-semibold text-brand-700 hover:underline">Volver al menú</a>
        </section>
    </main>
</x-guest-layout>
