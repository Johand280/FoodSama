<x-app-layout title="Editar producto">
    <x-page-header title="Editar producto" subtitle="Actualiza los datos de {{ $item->name }}.">
        <a href="{{ route('menu.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-neutral-200 bg-white px-4 py-2.5 text-sm font-semibold text-neutral-700 hover:bg-neutral-50">
            <x-icon name="arrow-right" class="size-4 rotate-180" />
            Volver al menú
        </a>
    </x-page-header>

    <x-flash />

    <div class="rounded-2xl border border-neutral-200 bg-white p-6 shadow-sm shadow-neutral-900/[0.02] sm:p-8">
        @include('menu._form', [
            'item' => $item,
            'action' => route('menu.update', $item),
            'method' => 'PUT',
            'submitLabel' => 'Guardar cambios',
        ])
    </div>
</x-app-layout>
