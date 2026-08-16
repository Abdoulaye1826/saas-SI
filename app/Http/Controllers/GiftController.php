<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGiftRequest;
use App\Models\Gift;
use App\Models\Product;
use App\Models\User;
use App\Services\GiftService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class GiftController extends Controller
{
    public function __construct(
        private readonly GiftService $giftService,
    ) {
    }

    /**
     * Historique des cadeaux (cahier §8) — dédié, distinct de l'historique
     * des ventes.
     */
    public function index(Request $request): View
    {
        $filters = $request->only(['search', 'customer_id', 'product_id', 'user_id', 'date_from', 'date_to']);
        $gifts = $this->giftService->paginate($filters);

        return view('gifts.index', [
            'gifts' => $gifts,
            'filters' => $filters,
            // Alimente les filtres "Produit"/"Utilisateur" du cahier §8.
            'products' => Product::orderBy('name')->get(['id', 'name']),
            'users' => User::orderBy('name')->get(['id', 'name']),
        ]);
    }

    /**
     * Soumis depuis le formulaire de vente (resources/views/sales/_form.blade.php)
     * quand "Cadeau / Produit offert" est sélectionné — jamais via sales.store.
     */
    public function store(StoreGiftRequest $request): RedirectResponse
    {
        try {
            $this->giftService->create($request->validated(), auth()->id());
        } catch (\RuntimeException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->route('gifts.index')
            ->with('success', 'Cadeau enregistré avec succès.');
    }

    public function print(Gift $gift): View
    {
        $gift->load(['customer', 'user', 'items.product', 'items.productImei']);
        $downloadUrl = route('gifts.download', $gift);

        return view('documents.gift_voucher', compact('gift', 'downloadUrl'));
    }

    public function download(Gift $gift): Response
    {
        $content = $this->giftService->renderPdfContent($gift);
        $fileName = "{$gift->gift_number}.pdf";

        return response($content, 200, [
            'Content-Type' => 'application/pdf; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ]);
    }

    /**
     * Annulation/correction (cahier §9) : restocke et marque le cadeau
     * annulé sans jamais supprimer la ligne d'historique.
     */
    public function cancel(Gift $gift): RedirectResponse
    {
        try {
            $this->giftService->cancel($gift, auth()->id());
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('gifts.index')
            ->with('success', 'Cadeau annulé, stock restauré.');
    }
}
