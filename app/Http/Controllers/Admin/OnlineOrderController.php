<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OnlineOrderStatus;
use App\Http\Controllers\Controller;
use App\Models\OnlineOrder;
use App\Services\OnlineOrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OnlineOrderController extends Controller
{
    public function __construct(private readonly OnlineOrderService $onlineOrderService)
    {
    }

    public function index(Request $request): View
    {
        $filters = $request->only(['search', 'status']);
        $orders = $this->onlineOrderService->paginate($filters);

        return view('admin.online-orders.index', compact('orders', 'filters'));
    }

    public function show(OnlineOrder $onlineOrder): View
    {
        $onlineOrder->load(['items.product', 'customer', 'assignedDriver', 'sale.invoice']);
        $drivers = $this->onlineOrderService->getDrivers();

        return view('admin.online-orders.show', ['order' => $onlineOrder, 'drivers' => $drivers]);
    }

    public function confirm(OnlineOrder $onlineOrder): RedirectResponse
    {
        try {
            $this->onlineOrderService->confirm($onlineOrder, auth()->id());
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', "Commande {$onlineOrder->order_number} confirmée : vente et facture générées.");
    }

    public function updateStatus(Request $request, OnlineOrder $onlineOrder): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:preparing,ready,shipped,delivered'],
            'assigned_driver_id' => ['nullable', 'exists:users,id'],
        ]);

        try {
            $this->onlineOrderService->updateStatus(
                $onlineOrder,
                OnlineOrderStatus::from($data['status']),
                $data['assigned_driver_id'] ?? null
            );
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Statut de la commande mis à jour.');
    }

    public function cancel(OnlineOrder $onlineOrder): RedirectResponse
    {
        try {
            $this->onlineOrderService->cancel($onlineOrder, auth()->id());
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', "Commande {$onlineOrder->order_number} annulée.");
    }
}
