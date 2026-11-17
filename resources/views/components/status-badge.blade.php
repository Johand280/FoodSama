@props(['status'])

@php
    $status = $status instanceof \App\OrderStatus
        ? $status
        : \App\OrderStatus::from((string) $status);
@endphp

<span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset {{ $status->badgeClass() }}">
    {{ $status->label() }}
</span>
