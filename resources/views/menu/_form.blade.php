<form method="POST" action="{{ $action }}" class="space-y-6">
    @csrf
    @if ($method ?? null)
        @method($method)
    @endif

    <div class="grid gap-5 sm:grid-cols-2">
        <div>
            <label for="category" class="mb-1.5 block text-sm font-semibold text-neutral-800">Categoría</label>
            <input id="category" name="category" type="text" list="menu-categories" value="{{ old('category', $item?->category ?? 'Platos principales') }}" required class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-3 text-sm outline-none transition focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10" placeholder="Ej. Platos principales">
            <datalist id="menu-categories">
                @foreach ($categories as $categoryOption)
                    <option value="{{ $categoryOption }}"></option>
                @endforeach
            </datalist>
        </div>

        <div>
            <label for="sort_order" class="mb-1.5 block text-sm font-semibold text-neutral-800">Orden <span class="font-normal text-neutral-400">(opcional)</span></label>
            <input id="sort_order" name="sort_order" type="number" min="0" value="{{ old('sort_order', $item?->sort_order ?? 0) }}" class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-3 text-sm outline-none transition focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10">
        </div>
    </div>

    <div>
        <label for="name" class="mb-1.5 block text-sm font-semibold text-neutral-800">Nombre del producto</label>
        <input id="name" name="name" type="text" value="{{ old('name', $item?->name) }}" required autofocus class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-3 text-sm outline-none transition focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10" placeholder="Ej. Lomo saltado">
    </div>

    <div>
        <label for="description" class="mb-1.5 block text-sm font-semibold text-neutral-800">Descripción <span class="font-normal text-neutral-400">(opcional)</span></label>
        <textarea id="description" name="description" rows="3" class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-3 text-sm outline-none transition focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10" placeholder="Ingredientes, tamaño o detalles que quieras mostrar">{{ old('description', $item?->description) }}</textarea>
    </div>

    <div class="grid gap-5 sm:grid-cols-2">
        <div>
            <label for="price" class="mb-1.5 block text-sm font-semibold text-neutral-800">Precio de venta</label>
            <div class="relative">
                <input id="price" name="price" type="number" step="0.01" min="0" value="{{ old('price', $item?->price ?? 0) }}" required class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-3 pr-12 text-sm outline-none transition focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10">
                <span class="absolute right-4 top-1/2 -translate-y-1/2 text-xs font-semibold text-neutral-400">{{ auth()->user()->business->currency }}</span>
            </div>
        </div>

        <div>
            <label for="cost" class="mb-1.5 block text-sm font-semibold text-neutral-800">Costo estimado <span class="font-normal text-neutral-400">(opcional)</span></label>
            <div class="relative">
                <input id="cost" name="cost" type="number" step="0.01" min="0" value="{{ old('cost', $item?->cost) }}" class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-3 pr-12 text-sm outline-none transition focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10">
                <span class="absolute right-4 top-1/2 -translate-y-1/2 text-xs font-semibold text-neutral-400">{{ auth()->user()->business->currency }}</span>
            </div>
            <p class="mt-1.5 text-xs text-neutral-500">Si se desconoce, el reporte la señalará como incompleta.</p>
        </div>
    </div>

    <label class="flex items-center gap-3 rounded-xl border border-neutral-200 bg-neutral-50 p-4">
        <input type="checkbox" name="is_available" value="1" @checked(old('is_available', $item?->is_available ?? true)) class="size-4 rounded border-neutral-300 text-brand-600 focus:ring-brand-500">
        <span>
            <span class="block text-sm font-semibold text-neutral-800">Disponible para vender</span>
            <span class="block text-xs text-neutral-500">Desmárcalo si temporalmente no lo ofreces.</span>
        </span>
    </label>

    <div class="flex flex-wrap items-center gap-3 border-t border-neutral-100 pt-5">
        <button type="submit" class="rounded-xl bg-brand-600 px-5 py-3 text-sm font-semibold text-white shadow-sm shadow-brand-600/20 hover:bg-brand-700">{{ $submitLabel }}</button>
        <a href="{{ route('menu.index') }}" class="rounded-xl border border-neutral-200 px-5 py-3 text-sm font-semibold text-neutral-700 hover:bg-neutral-50">Cancelar</a>
    </div>
</form>
