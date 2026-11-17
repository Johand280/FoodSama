@props(['href', 'label', 'icon', 'active' => false])

<a href="{{ $href }}" {{ $attributes->merge(['class' => $active
    ? 'flex items-center gap-3 rounded-xl bg-brand-50 px-3 py-2.5 text-sm font-semibold text-brand-800'
    : 'flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-neutral-600 transition hover:bg-neutral-100 hover:text-neutral-900']) }}>
    <x-icon :name="$icon" class="size-[18px]" />
    <span>{{ $label }}</span>
</a>
