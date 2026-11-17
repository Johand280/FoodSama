<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreExpenseRequest;
use App\Models\Expense;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ExpenseController extends Controller
{
    public function index(): View
    {
        $expenses = Expense::query()
            ->with('user')
            ->latest('expense_date')
            ->latest('id')
            ->paginate(15);

        return view('expenses.index', ['expenses' => $expenses]);
    }

    public function store(StoreExpenseRequest $request): RedirectResponse
    {
        $request->user()->business->expenses()->create([
            ...$request->validated(),
            'user_id' => $request->user()->id,
        ]);

        return redirect()->route('expenses.index')->with('status', 'Gasto registrado.');
    }
}
