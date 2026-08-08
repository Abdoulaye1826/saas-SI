<?php

namespace App\Http\Controllers\Storefront\Account;

use App\Http\Controllers\Controller;
use App\Models\OnlineOrder;
use App\Services\InvoiceService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function __construct(private readonly InvoiceService $invoiceService)
    {
    }

    public function index(): View
    {
        $orders = Auth::guard('customer')->user()
            ->onlineOrders()
            ->with('items')
            ->orderByDesc('id')
            ->paginate(10);

        return view('storefront.account.orders.index', compact('orders'));
    }

    public function show(OnlineOrder $onlineOrder): View
    {
        $this->authorizeOwnership($onlineOrder);

        $onlineOrder->load(['items.product', 'sale.invoice', 'assignedDriver']);

        return view('storefront.account.orders.show', ['order' => $onlineOrder]);
    }

    public function downloadInvoice(OnlineOrder $onlineOrder): Response
    {
        $this->authorizeOwnership($onlineOrder);

        $invoice = $onlineOrder->sale?->invoice;
        abort_if($invoice === null, 404, "Aucune facture n'est encore disponible pour cette commande.");

        $content = $this->invoiceService->renderPdfContent($invoice);

        return response($content, 200, [
            'Content-Type' => 'application/pdf; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$invoice->invoice_number}.pdf\"",
        ]);
    }

    private function authorizeOwnership(OnlineOrder $order): void
    {
        abort_unless($order->customer_id === Auth::guard('customer')->id(), 403);
    }
}
