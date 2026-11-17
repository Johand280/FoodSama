<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEmployeeRequest;
use Illuminate\Http\RedirectResponse;

class EmployeeController extends Controller
{
    public function store(StoreEmployeeRequest $request): RedirectResponse
    {
        $request->user()->business->users()->create([
            ...$request->validated(),
            'role' => 'employee',
        ]);

        return redirect()->route('settings.edit')->with('status', 'La cuenta del empleado fue creada.');
    }
}
