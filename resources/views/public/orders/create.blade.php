@use('App\OrderChannel')
@use('Illuminate\Support\Number')

<x-guest-layout :title="'Pedir · '.$business->name">
    <header class="border-b border-neutral-200 bg-white">
        <div class="mx-auto max-w-3xl px-5 py-5">
            <a href="{{ route('public.menu', $business->slug) }}" class="text-sm font-semibold text-brand-700 hover:underline">← {{ $business->name }}</a>
            <h1 class="mt-2 text-2xl font-bold text-neutral-950">Tu pedido</h1>
            <p class="mt-1 text-sm text-neutral-500">El pedido se guarda en el negocio antes de abrir WhatsApp.</p>
        </div>
    </header>

    <main class="mx-auto max-w-3xl px-5 py-8">
        <x-flash />
        @if ($itemsByCategory->isEmpty())
            <div class="rounded-2xl border border-neutral-200 bg-white p-8 text-center">
                <p class="text-neutral-700">No hay productos disponibles para pedir.</p>
                <a href="{{ route('public.menu', $business->slug) }}" class="mt-3 inline-block font-semibold text-brand-700">Volver al menú</a>
            </div>
        @else
            <form method="POST" action="{{ route('public.orders.store', $business->slug) }}" class="space-y-6">
                @csrf
                <input type="hidden" name="idempotency_key" value="{{ old('idempotency_key', $idempotencyKey) }}">

                <section class="grid gap-4 rounded-2xl border border-neutral-200 bg-white p-5 sm:grid-cols-2">
                    <div>
                        <label for="customer_name" class="mb-1 block text-sm font-semibold">Tu nombre</label>
                        <input id="customer_name" name="customer_name" value="{{ old('customer_name') }}" required maxlength="120" class="w-full rounded-xl border border-neutral-300 px-3 py-2.5 text-sm">
                        @error('customer_name')<p class="mt-1 text-sm text-rose-700">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="customer_phone" class="mb-1 block text-sm font-semibold">Tu teléfono</label>
                        <input id="customer_phone" name="customer_phone" type="tel" value="{{ old('customer_phone') }}" required maxlength="30" class="w-full rounded-xl border border-neutral-300 px-3 py-2.5 text-sm">
                        @error('customer_phone')<p class="mt-1 text-sm text-rose-700">{{ $message }}</p>@enderror
                    </div>
                    <div class="sm:col-span-2">
                        <label for="channel" class="mb-1 block text-sm font-semibold">Modalidad</label>
                        <select id="channel" name="channel" required class="w-full rounded-xl border border-neutral-300 px-3 py-2.5 text-sm">
                            @foreach (OrderChannel::options() as $channel)
                                <option value="{{ $channel['value'] }}" @selected(old('channel', 'takeaway') === $channel['value'])>{{ $channel['label'] }}</option>
                            @endforeach
                        </select>
                        @error('channel')<p class="mt-1 text-sm text-rose-700">{{ $message }}</p>@enderror
                    </div>
                    <div class="sm:col-span-2">
                        <label for="delivery_address" class="mb-1 block text-sm font-semibold">Dirección <span class="font-normal text-neutral-500">(obligatoria para domicilio)</span></label>
                        <input id="delivery_address" name="delivery_address" value="{{ old('delivery_address') }}" maxlength="255" class="w-full rounded-xl border border-neutral-300 px-3 py-2.5 text-sm">
                        @error('delivery_address')<p class="mt-1 text-sm text-rose-700">{{ $message }}</p>@enderror
                    </div>
                    <div class="sm:col-span-2">
                        <label for="notes" class="mb-1 block text-sm font-semibold">Observaciones <span class="font-normal text-neutral-500">(opcional)</span></label>
                        <textarea id="notes" name="notes" rows="2" maxlength="500" class="w-full rounded-xl border border-neutral-300 px-3 py-2.5 text-sm">{{ old('notes') }}</textarea>
                    </div>
                </section>

                <section class="space-y-3">
                    <div>
                        <h2 class="font-semibold text-neutral-950">Productos</h2>
                        <p class="mt-1 text-sm text-neutral-500">Usa cantidad cero para no agregar un producto.</p>
                    </div>
                    @error('items')<p class="rounded-xl bg-rose-50 p-3 text-sm text-rose-700">{{ $message }}</p>@enderror
                    @foreach ($itemsByCategory as $category => $items)
                        <div class="overflow-hidden rounded-2xl border border-neutral-200 bg-white">
                            <h3 class="border-b border-neutral-100 bg-neutral-50 px-4 py-3 text-sm font-semibold">{{ $category }}</h3>
                            <div class="divide-y divide-neutral-100">
                                @foreach ($items as $item)
                                    <label class="flex items-center justify-between gap-3 px-4 py-4">
                                        <span class="min-w-0">
                                            <span class="block font-medium">{{ $item->name }}</span>
                                            <span class="block text-sm text-neutral-500">{{ Number::currency($item->price, $business->currency, locale: 'es-CO') }}</span>
                                        </span>
                                        <input aria-label="Cantidad de {{ $item->name }}" type="number" name="items[{{ $item->id }}][quantity]" min="0" max="99" value="{{ old("items.{$item->id}.quantity", 0) }}" class="w-20 rounded-lg border border-neutral-300 px-2 py-2 text-center text-sm">
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </section>

                <button class="w-full rounded-xl bg-brand-600 px-5 py-3.5 text-sm font-semibold text-white hover:bg-brand-700">Guardar pedido</button>
            </form>
        @endif
    </main>
</x-guest-layout>
