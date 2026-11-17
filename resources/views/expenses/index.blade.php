@use('Illuminate\Support\Number')

<x-app-layout title="Gastos">
    @php $currency = auth()->user()->business->currency; @endphp

    <x-page-header title="Gastos operativos" subtitle="Registra arriendo, servicios u otros gastos del período para estimar el resultado del negocio." />

    <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_1.4fr]">
        <form method="POST" action="{{ route('expenses.store') }}" class="h-fit space-y-4 rounded-2xl border border-neutral-200 bg-white p-5">
            @csrf
            <h2 class="font-semibold text-neutral-950">Registrar gasto</h2>
            <div>
                <label for="concept" class="mb-1 block text-sm font-semibold">Concepto</label>
                <input id="concept" name="concept" value="{{ old('concept') }}" required maxlength="120" class="w-full rounded-xl border border-neutral-300 px-3 py-2.5 text-sm">
                @error('concept')<p class="mt-1 text-sm text-rose-700">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="amount" class="mb-1 block text-sm font-semibold">Valor ({{ $currency }})</label>
                <input id="amount" name="amount" type="number" step="0.01" min="0.01" value="{{ old('amount') }}" required class="w-full rounded-xl border border-neutral-300 px-3 py-2.5 text-sm">
                @error('amount')<p class="mt-1 text-sm text-rose-700">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="expense_date" class="mb-1 block text-sm font-semibold">Fecha del gasto</label>
                <input id="expense_date" name="expense_date" type="date" value="{{ old('expense_date', now()->toDateString()) }}" required class="w-full rounded-xl border border-neutral-300 px-3 py-2.5 text-sm">
                @error('expense_date')<p class="mt-1 text-sm text-rose-700">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="notes" class="mb-1 block text-sm font-semibold">Observaciones <span class="font-normal text-neutral-500">(opcional)</span></label>
                <textarea id="notes" name="notes" rows="2" maxlength="500" class="w-full rounded-xl border border-neutral-300 px-3 py-2.5 text-sm">{{ old('notes') }}</textarea>
            </div>
            <button class="w-full rounded-xl bg-brand-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-brand-700">Guardar gasto</button>
        </form>

        <section class="overflow-hidden rounded-2xl border border-neutral-200 bg-white">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[500px] text-left text-sm">
                    <thead class="bg-neutral-50 text-xs uppercase tracking-wide text-neutral-500">
                        <tr><th class="px-4 py-3">Fecha / concepto</th><th class="px-4 py-3">Registró</th><th class="px-4 py-3 text-right">Valor</th></tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100">
                        @forelse ($expenses as $expense)
                            <tr>
                                <td class="px-4 py-4">
                                    <p class="font-medium text-neutral-900">{{ $expense->concept }}</p>
                                    <p class="mt-1 text-xs text-neutral-500">{{ $expense->expense_date->format('d/m/Y') }}{{ $expense->notes ? ' · '.$expense->notes : '' }}</p>
                                </td>
                                <td class="px-4 py-4 text-neutral-600">{{ $expense->user?->name ?? '—' }}</td>
                                <td class="px-4 py-4 text-right font-semibold">{{ Number::currency($expense->amount, $currency, locale: 'es-CO') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-4 py-10 text-center text-neutral-500">Todavía no hay gastos registrados.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="border-t border-neutral-100 px-4 py-4">{{ $expenses->links() }}</div>
        </section>
    </div>
</x-app-layout>
