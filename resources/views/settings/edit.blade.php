<x-app-layout title="Configuración">
    <x-page-header title="Configuración del negocio" subtitle="Administra los datos del establecimiento y el acceso de tu equipo." />

    <div class="grid gap-6 lg:grid-cols-2">
        <section class="rounded-2xl border border-neutral-200 bg-white p-5">
            <h2 class="font-semibold text-neutral-950">Perfil público</h2>
            <form method="POST" action="{{ route('settings.update') }}" class="mt-5 space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label for="name" class="mb-1 block text-sm font-semibold">Nombre del negocio</label>
                    <input id="name" name="name" value="{{ old('name', $business->name) }}" required class="w-full rounded-xl border border-neutral-300 px-3 py-2.5 text-sm">
                    @error('name')<p class="mt-1 text-sm text-rose-700">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="phone" class="mb-1 block text-sm font-semibold">Teléfono empresarial</label>
                    <input id="phone" name="phone" type="tel" value="{{ old('phone', $business->phone) }}" required class="w-full rounded-xl border border-neutral-300 px-3 py-2.5 text-sm">
                    @error('phone')<p class="mt-1 text-sm text-rose-700">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="email" class="mb-1 block text-sm font-semibold">Correo del negocio</label>
                    <input id="email" name="email" type="email" value="{{ old('email', $business->email) }}" required class="w-full rounded-xl border border-neutral-300 px-3 py-2.5 text-sm">
                    @error('email')<p class="mt-1 text-sm text-rose-700">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="address" class="mb-1 block text-sm font-semibold">Dirección <span class="font-normal text-neutral-500">(opcional)</span></label>
                    <input id="address" name="address" value="{{ old('address', $business->address) }}" class="w-full rounded-xl border border-neutral-300 px-3 py-2.5 text-sm">
                    @error('address')<p class="mt-1 text-sm text-rose-700">{{ $message }}</p>@enderror
                </div>
                <button class="rounded-xl bg-brand-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-brand-700">Guardar cambios</button>
            </form>

            <div class="mt-6 border-t border-neutral-100 pt-5">
                <p class="text-sm font-semibold text-neutral-800">Menú público</p>
                <a href="{{ $publicMenuUrl }}" target="_blank" rel="noopener noreferrer" class="mt-2 inline-block break-all text-sm text-brand-700 underline">{{ $publicMenuUrl }}</a>
            </div>
        </section>

        <section class="space-y-5">
            <form method="POST" action="{{ route('settings.employees.store') }}" class="space-y-4 rounded-2xl border border-neutral-200 bg-white p-5">
                @csrf
                <h2 class="font-semibold text-neutral-950">Crear cuenta de empleado</h2>
                <p class="text-sm text-neutral-500">El empleado podrá atender pedidos y confirmar pagos, pero no verá costos, gastos ni rentabilidad.</p>
                <div>
                    <label for="employee_name" class="mb-1 block text-sm font-semibold">Nombre</label>
                    <input id="employee_name" name="name" value="{{ old('name') }}" required class="w-full rounded-xl border border-neutral-300 px-3 py-2.5 text-sm">
                    @error('name')<p class="mt-1 text-sm text-rose-700">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="employee_email" class="mb-1 block text-sm font-semibold">Correo</label>
                    <input id="employee_email" name="email" type="email" value="{{ old('email') }}" required class="w-full rounded-xl border border-neutral-300 px-3 py-2.5 text-sm">
                    @error('email')<p class="mt-1 text-sm text-rose-700">{{ $message }}</p>@enderror
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="employee_password" class="mb-1 block text-sm font-semibold">Contraseña inicial</label>
                        <input id="employee_password" name="password" type="password" required class="w-full rounded-xl border border-neutral-300 px-3 py-2.5 text-sm">
                        @error('password')<p class="mt-1 text-sm text-rose-700">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="employee_password_confirmation" class="mb-1 block text-sm font-semibold">Confirmar contraseña</label>
                        <input id="employee_password_confirmation" name="password_confirmation" type="password" required class="w-full rounded-xl border border-neutral-300 px-3 py-2.5 text-sm">
                    </div>
                </div>
                <button class="rounded-xl bg-neutral-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-neutral-800">Crear empleado</button>
            </form>

            <div class="rounded-2xl border border-neutral-200 bg-white p-5">
                <h2 class="font-semibold text-neutral-950">Equipo ({{ $employees->count() }})</h2>
                <ul class="mt-3 divide-y divide-neutral-100">
                    @forelse ($employees as $employee)
                        <li class="py-3">
                            <p class="font-medium text-neutral-900">{{ $employee->name }}</p>
                            <p class="text-sm text-neutral-500">{{ $employee->email }}</p>
                        </li>
                    @empty
                        <li class="py-4 text-sm text-neutral-500">Todavía no hay empleados.</li>
                    @endforelse
                </ul>
            </div>
        </section>
    </div>
</x-app-layout>
