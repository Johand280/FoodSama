<?php

namespace App\Http\Controllers;

use App\Actions\IssueReceipt;
use App\Models\Order;
use App\Models\Receipt;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReceiptController extends Controller
{
    /**
     * List issued receipts.
     */
    public function index(): View
    {
        $receipts = Receipt::query()
            ->with('order')
            ->latest('issued_at')
            ->paginate(15);

        return view('receipts.index', ['receipts' => $receipts]);
    }

    /**
     * Issue a receipt for the given order.
     */
    public function store(Request $request, Order $order, IssueReceipt $issueReceipt): RedirectResponse
    {
        $receipt = $issueReceipt->handle($request->user(), $order);

        return redirect()->route('receipts.show', $receipt)->with('status', "Comprobante {$receipt->number} emitido.");
    }

    /**
     * Show a printable receipt.
     */
    public function show(Receipt $receipt): View
    {
        $receipt->load(['order.items', 'business']);

        return view('receipts.show', [
            'receipt' => $receipt,
            'business' => $receipt->business,
            'order' => $receipt->order,
            'currency' => auth()->user()->business->currency,
        ]);
    }
}
