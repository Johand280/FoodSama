@php
    $business = auth()->user()->business;
@endphp

<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $title ?? 'Panel' }} · Food-Sama</title>

        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-[#f8f7f4] text-neutral-900 antialiased">
        <div class="flex min-h-screen">
            <!-- Sidebar -->
            <aside class="fixed inset-y-0 left-0 z-30 hidden w-64 flex-col border-r border-neutral-200 bg-white lg:flex">
                <div class="flex items-center gap-3 px-6 py-6">
                    <div class="flex size-10 items-center justify-center rounded-xl bg-brand-600 text-lg font-bold text-white shadow-sm shadow-brand-600/30">F</div>
                    <div>
                        <p class="text-base font-bold tracking-tight text-neutral-950">Food-Sama</p>
                        <p class="text-[11px] font-medium uppercase tracking-wider text-neutral-400">Software para food business</p>
                    </div>
                </div>

                <nav class="flex-1 space-y-1 px-3 py-4">
                    <p class="px-3 pb-2 text-[11px] font-semibold uppercase tracking-wider text-neutral-400">Operación</p>
                    <x-nav-link href="{{ route('dashboard') }}" label="Resumen" icon="dashboard" :active="request()->routeIs('dashboard')" />
                    <x-nav-link href="{{ route('orders.index') }}" label="Pedidos" icon="orders" :active="request()->routeIs('orders.*')" />
                    <x-nav-link href="{{ route('receipts.index') }}" label="Comprobantes" icon="receipt" :active="request()->routeIs('receipts.*')" />

                    @if (auth()->user()->isOwner())
                        <x-nav-link href="{{ route('menu.index') }}" label="Menú" icon="menu" :active="request()->routeIs('menu.*')" />
                        <x-nav-link href="{{ route('expenses.index') }}" label="Gastos" icon="receipt" :active="request()->routeIs('expenses.*')" />
                        <x-nav-link href="{{ route('profits.index') }}" label="Rentabilidad" icon="chart" :active="request()->routeIs('profits.*')" />

                        <p class="px-3 pt-6 pb-2 text-[11px] font-semibold uppercase tracking-wider text-neutral-400">Cuenta</p>
                        <x-nav-link href="{{ route('settings.edit') }}" label="Configuración" icon="settings" :active="request()->routeIs('settings.*')" />
                    @endif
                </nav>

                <div class="px-4 pb-4">
                    <div class="rounded-2xl bg-neutral-950 p-4 text-white">
                        <div class="flex items-center gap-2 text-xs font-medium text-brand-300">
                            <x-icon name="sparkles" class="size-4" />
                            Plan gratuito
                        </div>
                        <p class="mt-2 text-sm font-semibold">Tu negocio, siempre conectado</p>
                        <p class="mt-1 text-xs text-neutral-400">Cada establecimiento mantiene su información separada.</p>
                    </div>

                    <form method="POST" action="{{ route('logout') }}" class="mt-3">
                        @csrf
                        <button type="submit" class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-neutral-500 transition hover:bg-neutral-100 hover:text-neutral-900">
                            <x-icon name="logout" class="size-[18px]" />
                            Cerrar sesión
                        </button>
                    </form>
                </div>
            </aside>

            <!-- Content -->
            <div class="flex min-w-0 flex-1 flex-col lg:pl-64">
                <header class="sticky top-0 z-20 border-b border-neutral-200 bg-white/90 backdrop-blur">
                    <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-3 sm:px-6 lg:px-8">
                        <div class="flex min-w-0 items-center gap-3">
                            <details class="lg:hidden">
                                <summary class="flex size-10 cursor-pointer list-none items-center justify-center rounded-xl border border-neutral-200 text-neutral-600 [&::-webkit-details-marker]:hidden">
                                    <x-icon name="menu" class="size-5" />
                                </summary>
                                <div class="absolute left-4 top-16 z-40 w-64 rounded-2xl border border-neutral-200 bg-white p-3 shadow-xl">
                                    <x-nav-link href="{{ route('dashboard') }}" label="Resumen" icon="dashboard" :active="request()->routeIs('dashboard')" />
                                    <x-nav-link href="{{ route('orders.index') }}" label="Pedidos" icon="orders" :active="request()->routeIs('orders.*')" />
                                    <x-nav-link href="{{ route('receipts.index') }}" label="Comprobantes" icon="receipt" :active="request()->routeIs('receipts.*')" />
                                    @if (auth()->user()->isOwner())
                                        <x-nav-link href="{{ route('menu.index') }}" label="Menú" icon="menu" :active="request()->routeIs('menu.*')" />
                                        <x-nav-link href="{{ route('expenses.index') }}" label="Gastos" icon="receipt" :active="request()->routeIs('expenses.*')" />
                                        <x-nav-link href="{{ route('profits.index') }}" label="Rentabilidad" icon="chart" :active="request()->routeIs('profits.*')" />
                                        <x-nav-link href="{{ route('settings.edit') }}" label="Configuración" icon="settings" :active="request()->routeIs('settings.*')" />
                                    @endif
                                    <form method="POST" action="{{ route('logout') }}" class="mt-1">
                                        @csrf
                                        <button type="submit" class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-neutral-600 hover:bg-neutral-100">
                                            <x-icon name="logout" class="size-[18px]" />
                                            Cerrar sesión
                                        </button>
                                    </form>
                                </div>
                            </details>

                            <div class="min-w-0">
                                <p class="truncate text-sm font-semibold text-neutral-900">{{ $business->name }}</p>
                                <p class="flex items-center gap-1 text-xs text-neutral-500">
                                    <x-icon name="phone" class="size-3" />
                                    {{ $business->phone }}
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <a href="{{ route('orders.create') }}" class="hidden items-center gap-2 rounded-xl bg-brand-600 px-4 py-2 text-sm font-semibold text-white shadow-sm shadow-brand-600/25 transition hover:bg-brand-700 sm:inline-flex">
                                <x-icon name="plus" class="size-4" />
                                Nuevo pedido
                            </a>
                            <div class="flex size-9 items-center justify-center rounded-full bg-brand-100 text-sm font-bold text-brand-800">
                                {{ strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
                            </div>
                        </div>
                    </div>
                </header>

                <main class="mx-auto w-full max-w-7xl flex-1 px-4 py-8 sm:px-6 lg:px-8">
                    <x-flash />
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
