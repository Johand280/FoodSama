@use('Illuminate\Support\Number')

<x-guest-layout :title="$business->name.' · Menú'">
    <header class="border-b border-neutral-200 bg-white">
        <div class="mx-auto flex max-w-5xl flex-wrap items-center justify-between gap-4 px-5 py-5">
            <div>
                <p class="text-xs font-semibold uppercase tracking-widest text-brand-700">Food-Sama</p>
                <h1 class="mt-1 text-2xl font-bold text-neutral-950">{{ $business->name }}</h1>
                @if ($business->address)
                    <p class="mt-1 text-sm text-neutral-500">{{ $business->address }}</p>
                @endif
            </div>
            <a href="tel:{{ $business->phone }}" class="text-sm font-semibold text-brand-700">{{ $business->phone }}</a>
        </div>
    </header>

    <main class="mx-auto max-w-5xl px-5 py-10">
        <div class="mb-7 flex flex-wrap items-end justify-between gap-4">
            <div>
                <h2 class="text-xl font-semibold text-neutral-950">Menú disponible</h2>
                <p class="mt-1 text-sm text-neutral-500">Elige tus productos y envía el pedido al negocio.</p>
            </div>
            @if ($itemsByCategory->isNotEmpty())
                <a href="{{ route('public.orders.create', $business->slug) }}" class="rounded-xl bg-brand-600 px-5 py-3 text-sm font-semibold text-white hover:bg-brand-700">Hacer un pedido</a>
            @endif
        </div>

        @forelse ($itemsByCategory as $category => $items)
            <section class="mb-7">
                <h3 class="mb-3 text-lg font-semibold text-neutral-800">{{ $category }}</h3>
                <div class="grid gap-3 md:grid-cols-2">
                    @foreach ($items as $item)
                        <article class="rounded-2xl border border-neutral-200 bg-white p-5">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <h4 class="font-semibold text-neutral-950">{{ $item->name }}</h4>
                                    @if ($item->description)
                                        <p class="mt-1 text-sm text-neutral-500">{{ $item->description }}</p>
                                    @endif
                                </div>
                                <p class="shrink-0 font-semibold text-brand-800">{{ Number::currency($item->price, $business->currency, locale: 'es-CO') }}</p>
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>
        @empty
            <div class="rounded-2xl border border-dashed border-neutral-300 bg-white p-10 text-center text-neutral-500">Este negocio todavía no tiene productos disponibles.</div>
        @endforelse
    </main>
</x-guest-layout>
