@use('Illuminate\Support\Number')

<x-app-layout title="Menú">
    @php $currency = auth()->user()->business->currency; @endphp

    <x-page-header title="Menú" subtitle="Organiza tus productos, precios y costos para estimar tus ganancias.">
        <a href="{{ route('menu.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-brand-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm shadow-brand-600/20 hover:bg-brand-700">
            <x-icon name="plus" class="size-4" />
            Agregar producto
        </a>
    </x-page-header>

    <div class="mb-6 rounded-2xl border border-neutral-200 bg-white p-4 shadow-sm shadow-neutral-900/[0.02]">
        <form method="GET" action="{{ route('menu.index') }}" class="flex flex-col gap-3 sm:flex-row">
            <div class="relative flex-1">
                <x-icon name="search" class="absolute left-3.5 top-1/2 size-4 -translate-y-1/2 text-neutral-400" />
                <input type="search" name="search" value="{{ $search }}" placeholder="Buscar por nombre o descripción" class="w-full rounded-xl border border-neutral-200 bg-neutral-50 py-2.5 pl-10 pr-4 text-sm outline-none focus:border-brand-500 focus:bg-white focus:ring-4 focus:ring-brand-500/10">
            </div>
            <select name="category" class="rounded-xl border border-neutral-200 bg-neutral-50 px-4 py-2.5 text-sm outline-none focus:border-brand-500 focus:bg-white focus:ring-4 focus:ring-brand-500/10">
                <option value="">Todas las categorías</option>
                @foreach ($categories as $categoryOption)
                    <option value="{{ $categoryOption }}" @selected($category === $categoryOption)>{{ $categoryOption }}</option>
                @endforeach
            </select>
            <button type="submit" class="rounded-xl bg-neutral-900 px-5 py-2.5 text-sm font-semibold text-white hover:bg-neutral-800">Filtrar</button>
            @if ($search !== '' || $category !== '')
                <a href="{{ route('menu.index') }}" class="rounded-xl border border-neutral-200 px-4 py-2.5 text-center text-sm font-semibold text-neutral-600 hover:bg-neutral-50">Limpiar</a>
            @endif
        </form>
    </div>

    @if ($items->isEmpty())
        <div class="rounded-2xl border border-dashed border-neutral-300 bg-white px-6 py-16 text-center">
            <span class="mx-auto flex size-12 items-center justify-center rounded-2xl bg-brand-50 text-brand-700"><x-icon name="menu" /></span>
            <h2 class="mt-4 font-semibold text-neutral-900">No hay productos para mostrar</h2>
            <p class="mt-1 text-sm text-neutral-500">Agrega tu primer producto o ajusta los filtros.</p>
            <a href="{{ route('menu.create') }}" class="mt-6 inline-flex items-center gap-2 rounded-xl bg-brand-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-brand-700">
                <x-icon name="plus" class="size-4" />
                Agregar producto
            </a>
        </div>
    @else
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
            @foreach ($items as $item)
                <article class="flex flex-col rounded-2xl border border-neutral-200 bg-white p-5 shadow-sm shadow-neutral-900/[0.02] transition hover:border-brand-200 hover:shadow-md">
                    <div class="flex items-start justify-between gap-3">
                        <span class="rounded-full bg-neutral-100 px-2.5 py-1 text-[11px] font-semibold uppercase tracking-wide text-neutral-600">{{ $item->category }}</span>
                        <span class="inline-flex items-center gap-1.5 text-xs font-medium {{ $item->is_available ? 'text-emerald-600' : 'text-neutral-400' }}">
                            <span class="size-2 rounded-full {{ $item->is_available ? 'bg-emerald-500' : 'bg-neutral-300' }}"></span>
                            {{ $item->is_available ? 'Disponible' : 'No disponible' }}
                        </span>
                    </div>

                    <h3 class="mt-4 text-base font-semibold text-neutral-950">{{ $item->name }}</h3>
                    <p class="mt-1.5 line-clamp-2 min-h-10 text-sm text-neutral-500">{{ $item->description ?: 'Sin descripción' }}</p>

                    <div class="mt-5 grid grid-cols-3 gap-2 rounded-xl bg-neutral-50 p-3">
                        <div>
                            <p class="text-[11px] text-neutral-500">Precio</p>
                            <p class="mt-0.5 text-sm font-semibold text-neutral-900">{{ Number::currency($item->price, $currency, locale: 'es-CO') }}</p>
                        </div>
                        <div>
                            <p class="text-[11px] text-neutral-500">Costo</p>
                            <p class="mt-0.5 text-sm font-semibold text-neutral-700">{{ $item->cost === null ? 'Sin registrar' : Number::currency($item->cost, $currency, locale: 'es-CO') }}</p>
                        </div>
                        <div>
                            <p class="text-[11px] text-neutral-500">Margen</p>
                            <p class="mt-0.5 text-sm font-semibold text-emerald-600">{{ $item->margin() === null ? '—' : Number::currency($item->margin(), $currency, locale: 'es-CO') }}</p>
                        </div>
                    </div>

                    <div class="mt-5 flex items-center gap-2 border-t border-neutral-100 pt-4">
                        <form method="POST" action="{{ route('menu.availability', $item) }}" class="flex-1">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="w-full rounded-lg border border-neutral-200 px-3 py-2 text-xs font-semibold text-neutral-700 transition hover:bg-neutral-50">
                                {{ $item->is_available ? 'Marcar agotado' : 'Marcar disponible' }}
                            </button>
                        </form>
                        <a href="{{ route('menu.edit', $item) }}" class="flex size-9 items-center justify-center rounded-lg border border-neutral-200 text-neutral-600 hover:bg-neutral-50" title="Editar">
                            <x-icon name="edit" class="size-4" />
                        </a>
                        <form method="POST" action="{{ route('menu.destroy', $item) }}" data-confirm="¿Eliminar este producto del menú?">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="flex size-9 items-center justify-center rounded-lg border border-neutral-200 text-rose-600 hover:bg-rose-50" title="Eliminar">
                                <x-icon name="trash" class="size-4" />
                            </button>
                        </form>
                    </div>
                </article>
            @endforeach
        </div>

        <div class="mt-8">{{ $items->links() }}</div>
    @endif
</x-app-layout>
