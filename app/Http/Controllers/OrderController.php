<?php

namespace App\Http\Controllers;

use App\Actions\ConfirmOrderPayment;
use App\Actions\CreateOrder;
use App\Actions\UpdateOrderStatus as UpdateOrderStatusAction;
use App\Http\Requests\StoreOrderRequest;
use App\Models\Order;
use App\OrderOrigin;
use App\OrderStatus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class OrderController extends Controller
{
    /**
     * List the business orders.
     */
    public function index(Request $request): View
    {
        $status = trim((string) $request->query('status', ''));
        $search = trim((string) $request->query('search', ''));

        $orders = Order::query()
            ->with('items')
            ->when($status !== '', function ($query) use ($status): void {
                if ($status === 'active') {
                    $query->where(function ($query): void {
                        $query->whereIn('status', [OrderStatus::Pending, OrderStatus::Preparing, OrderStatus::Ready])
                            ->orWhere(function ($query): void {
                                $query->where('status', OrderStatus::Delivered)->whereNull('paid_at');
                            });
                    });
                } else {
                    $query->where('status', $status);
                }
            })
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('order_number', 'like', "%{$search}%")
                        ->orWhere('customer_name', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->latest('id')
            ->paginate(12)
            ->withQueryString();

        return view('orders.index', [
            'orders' => $orders,
            'status' => $status,
            'search' => $search,
            'statuses' => OrderStatus::options(),
        ]);
    }

    /**
     * Show the form for registering an order.
     */
    public function create(): View
    {
        $business = auth()->user()->business;
        $items = $business->menuItems()
            ->available()
            ->orderBy('category')
            ->orderBy('name')
            ->get()
            ->groupBy('category');

        return view('orders.create', [
            'itemsByCategory' => $items,
            'idempotencyKey' => (string) Str::uuid(),
            'origins' => OrderOrigin::staffOptions(),
        ]);
    }

    /**
     * Register a new order.
     */
    public function store(StoreOrderRequest $request, CreateOrder $createOrder): RedirectResponse
    {
        $order = $createOrder->handle(
            $request->user()->business,
            $request->validated(),
            $request->user(),
            OrderOrigin::from($request->validated('origin')),
        );

        return redirect()->route('orders.show', $order)->with('status', "Pedido {$order->order_number} registrado.");
    }

    /**
     * Show the order details.
     */
    public function show(Order $order): View
    {
        $order->load(['items', 'receipt', 'events.user']);

        return view('orders.show', [
            'order' => $order,
            'isOwner' => auth()->user()->isOwner(),
        ]);
    }

    /**
     * Update the order status.
     */
    public function updateStatus(
        Request $request,
        Order $order,
        UpdateOrderStatusAction $updateOrderStatus,
    ): RedirectResponse {
        $data = $request->validate([
            'status' => ['required', Rule::enum(OrderStatus::class)],
            'cancellation_reason' => ['nullable', 'required_if:status,cancelled', 'string', 'max:500'],
        ]);

        $newStatus = OrderStatus::from($data['status']);
        $updateOrderStatus->handle(
            $request->user(),
            $order,
            $newStatus,
            $data['cancellation_reason'] ?? null,
        );

        return back()->with('status', "Pedido actualizado a {$newStatus->label()}.");
    }

    public function confirmPayment(
        Request $request,
        Order $order,
        ConfirmOrderPayment $confirmOrderPayment,
    ): RedirectResponse {
        $confirmOrderPayment->handle($request->user(), $order);

        return back()->with('status', 'Pago confirmado.');
    }
}
