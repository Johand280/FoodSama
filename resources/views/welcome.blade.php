<x-guest-layout title="Food-Sama · Tu negocio de comida, bajo control">
    <div class="overflow-hidden">
        <header class="mx-auto flex max-w-7xl items-center justify-between px-6 py-6">
            <a href="{{ route('home') }}" class="flex items-center gap-3">
                <span class="flex size-10 items-center justify-center rounded-xl bg-brand-600 text-lg font-bold text-white shadow-lg shadow-brand-600/25">F</span>
                <span class="text-lg font-bold tracking-tight text-neutral-950">Food-Sama</span>
            </a>

            <nav class="hidden items-center gap-8 text-sm font-medium text-neutral-600 md:flex">
                <a href="#funciones" class="hover:text-neutral-950">Funciones</a>
                <a href="#como-funciona" class="hover:text-neutral-950">Cómo funciona</a>
                <a href="#precios" class="hover:text-neutral-950">Precios</a>
            </nav>

            <div class="flex items-center gap-3">
                @auth
                    <a href="{{ route('dashboard') }}" class="rounded-xl bg-neutral-950 px-4 py-2.5 text-sm font-semibold text-white hover:bg-neutral-800">Ir al panel</a>
                @else
                    <a href="{{ route('login') }}" class="hidden text-sm font-semibold text-neutral-700 hover:text-neutral-950 sm:block">Iniciar sesión</a>
                    <a href="{{ route('register') }}" class="rounded-xl bg-brand-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm shadow-brand-600/25 hover:bg-brand-700">Crear cuenta</a>
                @endauth
            </div>
        </header>

        <main>
            <section class="relative mx-auto max-w-7xl px-6 pb-16 pt-12 sm:pt-20">
                <div class="absolute -top-40 right-0 -z-10 size-[32rem] rounded-full bg-brand-200/40 blur-3xl"></div>
                <div class="absolute -bottom-32 -left-32 -z-10 size-[28rem] rounded-full bg-amber-200/40 blur-3xl"></div>

                <div class="grid items-center gap-14 lg:grid-cols-[1.05fr_1fr]">
                    <div>
                        <span class="inline-flex items-center gap-2 rounded-full border border-brand-200 bg-white px-3 py-1.5 text-xs font-semibold text-brand-800 shadow-sm">
                            <x-icon name="sparkles" class="size-3.5" />
                            Software as a Service para food business
                        </span>

                        <h1 class="mt-6 text-4xl font-semibold leading-[1.08] tracking-tight text-neutral-950 sm:text-6xl">
                            Tu cocina, tus pedidos,
                            <span class="text-brand-600">tus ganancias.</span>
                        </h1>

                        <p class="mt-6 max-w-xl text-lg leading-relaxed text-neutral-600">
                            Administra tu menú, registra pedidos en segundos, emite comprobantes y conoce cuánto gana tu negocio. Todo en una sola plataforma, con los datos de cada establecimiento completamente separados.
                        </p>

                        <div class="mt-8 flex flex-wrap items-center gap-3">
                            <a href="{{ route('register') }}" class="inline-flex items-center gap-2 rounded-xl bg-brand-600 px-6 py-3.5 text-sm font-semibold text-white shadow-lg shadow-brand-600/25 transition hover:-translate-y-0.5 hover:bg-brand-700">
                                Empezar gratis
                                <x-icon name="arrow-right" class="size-4" />
                            </a>
                            <a href="#funciones" class="inline-flex items-center gap-2 rounded-xl border border-neutral-200 bg-white px-6 py-3.5 text-sm font-semibold text-neutral-800 shadow-sm transition hover:border-neutral-300">
                                Ver funciones
                            </a>
                        </div>

                        <dl class="mt-12 grid max-w-lg grid-cols-3 gap-6 border-t border-neutral-200 pt-6">
                            <div>
                                <dt class="text-2xl font-semibold text-neutral-950">100%</dt>
                                <dd class="mt-1 text-xs text-neutral-500">Datos por negocio</dd>
                            </div>
                            <div>
                                <dt class="text-2xl font-semibold text-neutral-950">24/7</dt>
                                <dd class="mt-1 text-xs text-neutral-500">Acceso desde cualquier lugar</dd>
                            </div>
                            <div>
                                <dt class="text-2xl font-semibold text-neutral-950">4 en 1</dt>
                                <dd class="mt-1 text-xs text-neutral-500">Menú, pedidos, ventas y reportes</dd>
                            </div>
                        </dl>
                    </div>

                    <div class="relative">
                        <div class="absolute -inset-4 -z-10 rounded-[2.5rem] bg-gradient-to-tr from-brand-200/60 to-amber-100/60 blur-2xl"></div>
                        <div class="rounded-3xl border border-white/80 bg-white p-4 shadow-2xl shadow-neutral-900/10 sm:p-6">
                            <div class="flex items-center justify-between border-b border-neutral-100 pb-4">
                                <div>
                                    <p class="text-xs text-neutral-500">Resumen de hoy</p>
                                    <p class="text-lg font-semibold text-neutral-950">Sabor & Co.</p>
                                </div>
                                <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">● En vivo</span>
                            </div>

                            <div class="mt-5 grid grid-cols-3 gap-3">
                                <div class="rounded-2xl bg-brand-50 p-3">
                                    <p class="text-[11px] text-brand-700">Ventas</p>
                                    <p class="mt-1 text-lg font-bold text-brand-900">$ 842</p>
                                </div>
                                <div class="rounded-2xl bg-emerald-50 p-3">
                                    <p class="text-[11px] text-emerald-700">Ganancia</p>
                                    <p class="mt-1 text-lg font-bold text-emerald-800">$ 318</p>
                                </div>
                                <div class="rounded-2xl bg-blue-50 p-3">
                                    <p class="text-[11px] text-blue-700">Pedidos</p>
                                    <p class="mt-1 text-lg font-bold text-blue-900">18</p>
                                </div>
                            </div>

                            <div class="mt-6 rounded-2xl border border-neutral-100 p-4">
                                <p class="text-sm font-semibold text-neutral-900">Ventas de la semana</p>
                                <div class="mt-4 flex h-32 items-end gap-2">
                                    @foreach ([45, 68, 52, 88, 72, 96, 80] as $height)
                                        <div class="flex flex-1 items-end">
                                            <div class="w-full rounded-t-lg bg-brand-500/80" style="height: {{ $height }}%"></div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="mt-4 space-y-2">
                                <div class="flex items-center justify-between rounded-xl bg-neutral-50 px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <span class="flex size-8 items-center justify-center rounded-lg bg-amber-100 text-amber-700"><x-icon name="bag" class="size-4" /></span>
                                        <div>
                                            <p class="text-sm font-semibold text-neutral-800">Pedido #PED-000184</p>
                                            <p class="text-xs text-neutral-500">Camila Torres · En preparación</p>
                                        </div>
                                    </div>
                                    <span class="text-sm font-semibold text-neutral-900">$ 73.00</span>
                                </div>
                                <div class="flex items-center justify-between rounded-xl bg-neutral-50 px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <span class="flex size-8 items-center justify-center rounded-lg bg-emerald-100 text-emerald-700"><x-icon name="check" class="size-4" /></span>
                                        <div>
                                            <p class="text-sm font-semibold text-neutral-800">Comprobante C-000092</p>
                                            <p class="text-xs text-neutral-500">Emitido hace 5 min</p>
                                        </div>
                                    </div>
                                    <span class="text-sm font-semibold text-neutral-900">$ 46.00</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section id="funciones" class="border-y border-neutral-200 bg-white py-20">
                <div class="mx-auto max-w-7xl px-6">
                    <div class="max-w-2xl">
                        <p class="text-sm font-semibold uppercase tracking-wider text-brand-600">Todo lo que necesitas</p>
                        <h2 class="mt-3 text-3xl font-semibold tracking-tight text-neutral-950 sm:text-4xl">Una operación más ordenada desde el primer día</h2>
                        <p class="mt-4 text-neutral-600">Diseñado para negocios de comida que quieren dejar atrás las hojas de cálculo y los mensajes dispersos.</p>
                    </div>

                    <div class="mt-12 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                        @foreach ([
                            ['icon' => 'menu', 'title' => 'Menú centralizado', 'text' => 'Organiza productos por categoría, precios, costos y disponibilidad.'],
                            ['icon' => 'orders', 'title' => 'Pedidos claros', 'text' => 'Recibe y registra pedidos con cliente, canal, productos y estados.'],
                            ['icon' => 'receipt', 'title' => 'Comprobantes listos', 'text' => 'Emite comprobantes numerados e imprímelos con los datos de tu negocio.'],
                            ['icon' => 'chart', 'title' => 'Ganancias estimadas', 'text' => 'Conoce ingresos, costos, margen y tus productos más rentables.'],
                        ] as $feature)
                            <div class="rounded-2xl border border-neutral-200 bg-white p-6 transition hover:-translate-y-1 hover:border-brand-200 hover:shadow-lg hover:shadow-neutral-900/5">
                                <span class="flex size-11 items-center justify-center rounded-xl bg-brand-50 text-brand-700"><x-icon :name="$feature['icon']" /></span>
                                <h3 class="mt-5 font-semibold text-neutral-950">{{ $feature['title'] }}</h3>
                                <p class="mt-2 text-sm leading-relaxed text-neutral-500">{{ $feature['text'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>

            <section id="como-funciona" class="mx-auto max-w-7xl px-6 py-20">
                <div class="grid gap-12 lg:grid-cols-2 lg:items-center">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-wider text-brand-600">Cómo funciona</p>
                        <h2 class="mt-3 text-3xl font-semibold tracking-tight text-neutral-950 sm:text-4xl">Configura tu negocio y empieza a vender</h2>
                        <p class="mt-4 text-neutral-600">Cada cuenta pertenece a un establecimiento. Tu menú, pedidos, comprobantes y reportes nunca se mezclan con los de otro negocio.</p>

                        <div class="mt-8 space-y-5">
                            @foreach ([
                                '1' => 'Regístrate con el nombre y teléfono de tu negocio.',
                                '2' => 'Carga tu menú con precios y costos de cada producto.',
                                '3' => 'Registra pedidos, emite comprobantes y revisa tus ganancias.',
                            ] as $number => $text)
                                <div class="flex gap-4">
                                    <span class="flex size-8 shrink-0 items-center justify-center rounded-full bg-brand-600 text-sm font-bold text-white">{{ $number }}</span>
                                    <p class="pt-1 text-sm leading-relaxed text-neutral-700">{{ $text }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="rounded-3xl bg-neutral-950 p-8 text-white sm:p-10">
                        <x-icon name="phone" class="size-8 text-brand-400" />
                        <h3 class="mt-6 text-2xl font-semibold">Un teléfono empresarial, una identidad clara</h3>
                        <p class="mt-3 text-sm leading-relaxed text-neutral-400">Tus comprobantes muestran el nombre, teléfono y dirección de tu negocio. Tus clientes siempre sabrán con quién están hablando.</p>
                        <div class="mt-8 rounded-2xl border border-white/10 bg-white/5 p-5">
                            <p class="text-xs uppercase tracking-wider text-neutral-500">Datos del negocio</p>
                            <p class="mt-3 text-sm font-semibold">Sabor & Co.</p>
                            <p class="mt-1 text-sm text-neutral-400">+57 300 123 4567</p>
                            <p class="mt-1 text-sm text-neutral-400">Lima 123, Miraflores</p>
                        </div>
                    </div>
                </div>
            </section>

            <section id="precios" class="pb-20">
                <div class="mx-auto max-w-5xl px-6">
                    <div class="rounded-3xl bg-brand-600 px-8 py-12 text-center text-white shadow-xl shadow-brand-600/20 sm:px-16 sm:py-16">
                        <h2 class="text-3xl font-semibold tracking-tight sm:text-4xl">Empieza gratis, crece cuando quieras</h2>
                        <p class="mx-auto mt-4 max-w-xl text-brand-50">Crea tu cuenta, configura tu menú y registra tus primeros pedidos sin costo. Una plataforma pensada para crecer junto a tu negocio.</p>
                        <a href="{{ route('register') }}" class="mt-8 inline-flex items-center gap-2 rounded-xl bg-white px-6 py-3.5 text-sm font-semibold text-brand-700 shadow-sm transition hover:-translate-y-0.5">
                            Crear mi cuenta
                            <x-icon name="arrow-right" class="size-4" />
                        </a>
                    </div>
                </div>
            </section>
        </main>

        <footer class="border-t border-neutral-200 bg-white py-8">
            <div class="mx-auto flex max-w-7xl flex-col items-center justify-between gap-3 px-6 text-sm text-neutral-500 sm:flex-row">
                <p>© {{ now()->year }} Food-Sama. Hecho para negocios de comida.</p>
                <p>Software as a Service · Cada negocio, su propio espacio.</p>
            </div>
        </footer>
    </div>
</x-guest-layout>
