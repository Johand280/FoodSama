<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateBusinessSettingsRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BusinessSettingsController extends Controller
{
    /**
     * Show the business settings form.
     */
    public function edit(Request $request): View
    {
        return view('settings.edit', [
            'business' => $request->user()->business,
            'employees' => $request->user()->business->users()
                ->where('role', 'employee')
                ->orderBy('name')
                ->get(),
            'publicMenuUrl' => route('public.menu', $request->user()->business->slug),
        ]);
    }

    /**
     * Update the business profile.
     */
    public function update(UpdateBusinessSettingsRequest $request): RedirectResponse
    {
        $request->user()->business->update($request->validated());

        return back()->with('status', 'Datos del negocio actualizados.');
    }
}
