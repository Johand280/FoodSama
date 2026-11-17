<x-guest-layout title="Crear cuenta · Food-Sama">
    <div class="grid min-h-screen lg:grid-cols-2">
        <div class="hidden flex-col justify-between bg-neutral-950 p-12 text-white lg:flex">
            <a href="{{ route('home') }}" class="flex items-center gap-3">
                <span class="flex size-10 items-center justify-center rounded-xl bg-brand-600 text-lg font-bold">F</span>
                <span class="text-lg font-bold tracking-tight">Food-Sama</span>
            </a>

            <div class="max-w-md">
                <p class="text-sm font-semibold uppercase tracking-wider text-brand-400">SaaS para food business</p>
                <h2 class="mt-4 text-4xl font-semibold leading-tight">Tu negocio tiene su propio espacio aquí.</h2>
                <p class="mt-5 text-neutral-400">Menú, pedidos, comprobantes y ganancias separados de cualquier otro establecimiento.</p>
            </div>

            <div class="rounded-2xl border border-white/10 bg-white/5 p-5">
                <p class="text-sm text-neutral-300">“Ahora registro los pedidos enseguida y sé exactamente cuánto gano cada día.”</p>
                <p class="mt-3 text-xs text-brand-300">Negocios que usan Food-Sama</p>
            </div>
        </div>

        <div class="flex items-center justify-center px-6 py-12">
            <div class="w-full max-w-md">
                <div class="mb-8 lg:hidden">
                    <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-lg font-bold text-neutral-950">
                        <span class="flex size-9 items-center justify-center rounded-xl bg-brand-600 text-white">F</span>
                        Food-Sama
                    </a>
                </div>

                <h1 class="text-3xl font-semibold tracking-tight text-neutral-950">Crea tu cuenta</h1>
                <p class="mt-2 text-sm text-neutral-500">Solo toma un minuto. Necesitas los datos de tu negocio.</p>

                <x-flash />

                <form method="POST" action="{{ route('register') }}" class="mt-8 space-y-5">
                    @csrf

                    <div>
                        <label for="name" class="mb-1.5 block text-sm font-semibold text-neutral-800">Tu nombre</label>
                        <input id="name" name="name" type="text" value="{{ old('name') }}" autocomplete="name" required autofocus class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-3 text-sm outline-none transition placeholder:text-neutral-400 focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10" placeholder="Ana Pérez">
                    </div>

                    <div>
                        <label for="business_name" class="mb-1.5 block text-sm font-semibold text-neutral-800">Nombre del negocio</label>
                        <input id="business_name" name="business_name" type="text" value="{{ old('business_name') }}" required class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-3 text-sm outline-none transition placeholder:text-neutral-400 focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10" placeholder="Sabor & Co.">
                    </div>

                    <div>
                        <label for="business_phone" class="mb-1.5 block text-sm font-semibold text-neutral-800">Teléfono empresarial</label>
                        <input id="business_phone" name="business_phone" type="tel" value="{{ old('business_phone') }}" required class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-3 text-sm outline-none transition placeholder:text-neutral-400 focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10" placeholder="+57 300 123 4567">
                    </div>

                    <div>
                        <label for="email" class="mb-1.5 block text-sm font-semibold text-neutral-800">Correo electrónico</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-3 text-sm outline-none transition placeholder:text-neutral-400 focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10" placeholder="hola@ tunegocio.com">
                    </div>

                    <div class="grid gap-5 sm:grid-cols-2">
                        <div>
                            <label for="password" class="mb-1.5 block text-sm font-semibold text-neutral-800">Contraseña</label>
                            <input id="password" name="password" type="password" autocomplete="new-password" required class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-3 text-sm outline-none transition focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10" placeholder="Mínimo 8 caracteres">
                        </div>
                        <div>
                            <label for="password_confirmation" class="mb-1.5 block text-sm font-semibold text-neutral-800">Confirmar</label>
                            <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-3 text-sm outline-none transition focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10" placeholder="Repite la contraseña">
                        </div>
                    </div>

                    <button type="submit" class="w-full rounded-xl bg-brand-600 px-5 py-3.5 text-sm font-semibold text-white shadow-lg shadow-brand-600/20 transition hover:bg-brand-700">
                        Crear cuenta gratis
                    </button>
                </form>

                <p class="mt-6 text-center text-sm text-neutral-500">
                    ¿Ya tienes cuenta?
                    <a href="{{ route('login') }}" class="font-semibold text-brand-700 hover:underline">Inicia sesión</a>
                </p>
            </div>
        </div>
    </div>
</x-guest-layout>
