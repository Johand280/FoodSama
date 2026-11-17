@use('App\OrderChannel')

<x-app-layout title="Registrar pedido">
    <x-page-header title="Registrar pedido" subtitle="Guarda pedidos recibidos por teléfono o chat; el total se calcula en el servidor." />

    @if ($itemsByCategory->isEmpty())
        <div class="rounded-2xl border border-dashed border-neutral-300 bg-white p-10 text-center">
            <p class="font-semibold text-neutral-900">No hay productos disponibles</p>
            @if (auth()->user()->isOwner())
                <a href="{{ route('menu.create') }}" class="mt-3 inline-flex font-semibold text-brand-700 hover:underline">Agrega productos al menú</a>
            @else
                <p class="mt-2 text-sm text-neutral-500">Pide al propietario que agregue productos disponibles.</p>
            @endif
        </div>
    @else
        <form method="POST" action="{{ route('orders.store') }}" class="space-y-6">
            @csrf
            <input type="hidden" name="idempotency_key" value="{{ old('idempotency_key', $idempotencyKey) }}">

            <section class="grid gap-5 rounded-2xl border border-neutral-200 bg-white p-5 sm:grid-cols-2">
                <div>
                    <label for="customer_name" class="mb-1.5 block text-sm font-semibold">Nombre del cliente</label>
                    <input id="customer_name" name="customer_name" value="{{ old('customer_name') }}" required maxlength="120" class="w-full rounded-xl border border-neutral-300 px-4 py-3 text-sm">
                    @error('customer_name')<p class="mt-1 text-sm text-rose-700">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="customer_phone" class="mb-1.5 block text-sm font-semibold">Teléfono del cliente</label>
                    <input id="customer_phone" name="customer_phone" type="tel" value="{{ old('customer_phone') }}" required maxlength="30" class="w-full rounded-xl border border-neutral-300 px-4 py-3 text-sm">
                    @error('customer_phone')<p class="mt-1 text-sm text-rose-700">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="origin" class="mb-1.5 block text-sm font-semibold">Recibido por</label>
                    <select id="origin" name="origin" required class="w-full rounded-xl border border-neutral-300 px-4 py-3 text-sm">
                        @foreach ($origins as $origin)
                            <option value="{{ $origin['value'] }}" @selected(old('origin', 'phone') === $origin['value'])>{{ $origin['label'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="channel" class="mb-1.5 block text-sm font-semibold">Modalidad de entrega</label>
                    <select id="channel" name="channel" required class="w-full rounded-xl border border-neutral-300 px-4 py-3 text-sm">
                        @foreach (OrderChannel::options() as $channel)
                            <option value="{{ $channel['value'] }}" @selected(old('channel', 'takeaway') === $channel['value'])>{{ $channel['label'] }}</option>
                        @endforeach
                    </select>
                    @error('channel')<p class="mt-1 text-sm text-rose-700">{{ $message }}</p>@enderror
                </div>
                <div class="sm:col-span-2">
                    <label for="delivery_address" class="mb-1.5 block text-sm font-semibold">Dirección <span class="font-normal text-neutral-500">(obligatoria para domicilio)</span></label>
                    <input id="delivery_address" name="delivery_address" value="{{ old('delivery_address') }}" maxlength="255" class="w-full rounded-xl border border-neutral-300 px-4 py-3 text-sm">
                    @error('delivery_address')<p class="mt-1 text-sm text-rose-700">{{ $message }}</p>@enderror
                </div>
                <div class="sm:col-span-2">
                    <label for="notes" class="mb-1.5 block text-sm font-semibold">Observaciones <span class="font-normal text-neutral-500">(opcional)</span></label>
                    <textarea id="notes" name="notes" rows="2" maxlength="500" class="w-full rounded-xl border border-neutral-300 px-4 py-3 text-sm">{{ old('notes') }}</textarea>
                </div>
            </section>

            <section class="space-y-4">
                <div>
                    <h2 class="text-lg font-semibold text-neutral-950">Productos</h2>
                    <p class="mt-1 text-sm text-neutral-500">Deja la cantidad en cero para omitir un producto.</p>
                </div>
                @error('items')<p class="rounded-xl bg-rose-50 p-3 text-sm text-rose-700">{{ $message }}</p>@enderror
                @foreach ($itemsByCategory as $category => $items)
                    <div class="overflow-hidden rounded-2xl border border-neutral-200 bg-white">
                        <h3 class="border-b border-neutral-100 bg-neutral-50 px-5 py-3 text-sm font-semibold">{{ $category }}</h3>
                        <div class="divide-y divide-neutral-100">
                            @foreach ($items as $item)
                                <label class="flex items-center justify-between gap-4 px-5 py-4">
                                    <span class="min-w-0">
                                        <span class="block font-medium text-neutral-900">{{ $item->name }}</span>
                                        <span class="mt-1 block text-sm text-neutral-500">{{ $item->description }}</span>
                                    </span>
                                    <span class="flex shrink-0 items-center gap-3">
                                        <span class="text-sm font-semibold">{{ \Illuminate\Support\Number::currency($item->price, auth()->user()->business->currency, locale: 'es-CO') }}</span>
                                        <input aria-label="Cantidad de {{ $item->name }}" type="number" name="items[{{ $item->id }}][quantity]" min="0" max="99" value="{{ old("items.{$item->id}.quantity", 0) }}" class="w-20 rounded-lg border border-neutral-300 px-2 py-2 text-center text-sm">
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach
                @error('items.*.quantity')<p class="text-sm text-rose-700">{{ $message }}</p>@enderror
            </section>

            <div class="flex flex-wrap gap-3">
                <button type="submit" class="rounded-xl bg-brand-600 px-5 py-3 text-sm font-semibold text-white hover:bg-brand-700">Guardar pedido</button>
                <a href="{{ route('orders.index') }}" class="rounded-xl border border-neutral-300 px-5 py-3 text-sm font-semibold text-neutral-700">Cancelar</a>
            </div>
        </form>
    @endif
</x-app-layout>
