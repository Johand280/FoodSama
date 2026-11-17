<x-guest-layout title="Iniciar sesión · Food-Sama">
    <div class="grid min-h-screen lg:grid-cols-2">
        <div class="flex items-center justify-center px-6 py-12">
            <div class="w-full max-w-md">
                <div class="mb-8 lg:hidden">
                    <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-lg font-bold text-neutral-950">
                        <span class="flex size-9 items-center justify-center rounded-xl bg-brand-600 text-white">F</span>
                        Food-Sama
                    </a>
                </div>

                <h1 class="text-3xl font-semibold tracking-tight text-neutral-950">Bienvenido de vuelta</h1>
                <p class="mt-2 text-sm text-neutral-500">Ingresa para continuar administrando tu negocio.</p>

                <x-flash />

                <form method="POST" action="{{ route('login') }}" class="mt-8 space-y-5">
                    @csrf

                    <div>
                        <label for="email" class="mb-1.5 block text-sm font-semibold text-neutral-800">Correo electrónico</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required autofocus class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-3 text-sm outline-none transition placeholder:text-neutral-400 focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10" placeholder="hola@tunegocio.com">
                    </div>

                    <div>
                        <div class="mb-1.5 flex items-center justify-between">
                            <label for="password" class="text-sm font-semibold text-neutral-800">Contraseña</label>
                        </div>
                        <input id="password" name="password" type="password" autocomplete="current-password" required class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-3 text-sm outline-none transition focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10" placeholder="Tu contraseña">
                    </div>

                    <label class="flex items-center gap-2 text-sm text-neutral-600">
                        <input type="checkbox" name="remember" value="1" class="size-4 rounded border-neutral-300 text-brand-600 focus:ring-brand-500">
                        Mantener la sesión iniciada
                    </label>

                    <button type="submit" class="w-full rounded-xl bg-brand-600 px-5 py-3.5 text-sm font-semibold text-white shadow-lg shadow-brand-600/20 transition hover:bg-brand-700">
                        Iniciar sesión
                    </button>
                </form>

                <div class="mt-6 rounded-xl border border-brand-100 bg-brand-50 p-4 text-sm text-brand-900">
                    <p class="font-semibold">Cuenta de demostración</p>
                    <p class="mt-1 text-brand-800">Correo: <span class="font-mono">demo@foodsama.test</span></p>
                    <p class="text-brand-800">Contraseña: <span class="font-mono">password</span></p>
                </div>

                <p class="mt-6 text-center text-sm text-neutral-500">
                    ¿Aún no tienes cuenta?
                    <a href="{{ route('register') }}" class="font-semibold text-brand-700 hover:underline">Crea una aquí</a>
                </p>
            </div>
        </div>

        <div class="relative hidden overflow-hidden bg-neutral-950 lg:flex lg:items-center lg:justify-center">
            <div class="absolute -right-32 -top-32 size-[32rem] rounded-full bg-brand-500/20 blur-3xl"></div>
            <div class="absolute -bottom-32 -left-32 size-[28rem] rounded-full bg-amber-500/10 blur-3xl"></div>
            <div class="relative max-w-md px-12 text-white">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-3">
                    <span class="flex size-10 items-center justify-center rounded-xl bg-brand-600 text-lg font-bold">F</span>
                    <span class="text-lg font-bold tracking-tight">Food-Sama</span>
                </a>
                <h2 class="mt-12 text-4xl font-semibold leading-tight">Todo tu negocio, en un mismo lugar.</h2>
                <p class="mt-5 text-neutral-400">Registra pedidos, organiza tu carta y entiende tus números sin complicaciones.</p>
            </div>
        </div>
    </div>
</x-guest-layout>
