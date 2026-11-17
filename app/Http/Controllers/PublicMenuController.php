<?php

namespace App\Http\Controllers;

use App\Actions\CreateOrder;
use App\Http\Requests\StorePublicOrderRequest;
use App\Models\Business;
use App\OrderOrigin;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Number;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PublicMenuController extends Controller
{
    public function index(Business $business): View
    {
        $items = $business->menuItems()
            ->available()
            ->orderBy('category')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->groupBy('category');

        return view('public.menu', [
            'business' => $business,
            'itemsByCategory' => $items,
        ]);
    }

    public function create(Business $business): View
    {
        $items = $business->menuItems()
            ->available()
            ->orderBy('category')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->groupBy('category');

        return view('public.orders.create', [
            'business' => $business,
            'itemsByCategory' => $items,
            'idempotencyKey' => (string) Str::uuid(),
        ]);
    }

    public function store(
        StorePublicOrderRequest $request,
        Business $business,
        CreateOrder $createOrder,
    ): RedirectResponse {
        $order = $createOrder->handle(
            $business,
            $request->validated(),
            null,
            OrderOrigin::Web,
        );

        return redirect()->route('public.orders.show', [
            'business' => $business->slug,
            'token' => $order->public_token,
        ]);
    }

    public function show(Business $business, string $token): View
    {
        $order = $business->orders()
            ->where('public_token', $token)
            ->with('items')
            ->firstOrFail();

        $phone = preg_replace('/\D+/', '', $business->phone) ?? '';
        $items = $order->items
            ->map(fn ($item): string => "{$item->quantity} × {$item->name}")
            ->implode("\n");
        $message = implode("\n", [
            "Hola, acabo de registrar el pedido {$order->order_number}.",
            "Cliente: {$order->customer_name}",
            $items,
            'Total: '.Number::currency($order->total, 'COP', locale: 'es-CO'),
        ]);

        return view('public.orders.show', [
            'business' => $business,
            'order' => $order,
            'whatsappUrl' => 'https://wa.me/'.$phone.'?text='.rawurlencode($message),
        ]);
    }
}
