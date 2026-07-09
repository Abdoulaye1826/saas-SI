<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreQuoteRequest;
use App\Http\Requests\UpdateQuoteRequest;
use App\Models\Quote;
use App\Services\QuoteService;
use App\Services\WhatsAppService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\URL;
use Illuminate\View\View;

class QuoteController extends Controller
{
    public function __construct(
        private readonly QuoteService $quoteService,
        private readonly WhatsAppService $whatsAppService
    ) {
    }

    public function index(Request $request): View
    {
        $filters = $request->only(['search', 'status', 'customer_id']);
        $quotes = $this->quoteService->paginate($filters);
        $summary = $this->quoteService->summary();

        return view('quotes.index', compact('quotes', 'filters', 'summary'));
    }

    public function create(): View
    {
        return view('quotes.create', [
            'customers' => $this->quoteService->getCustomers(),
            'products' => $this->quoteService->getProducts(),
            'categories' => $this->quoteService->getCategories(),
            'quote' => null,
        ]);
    }

    public function store(StoreQuoteRequest $request): RedirectResponse
    {
        $this->quoteService->create($request->validated(), auth()->id());

        return redirect()->route('quotes.index')
            ->with('success', 'Devis créé avec succès.');
    }

    public function edit(Quote $quote): View
    {
        $quote->load('items.product');

        return view('quotes.edit', [
            'quote' => $quote,
            'customers' => $this->quoteService->getCustomers(),
            'products' => $this->quoteService->getProducts(),
            'categories' => $this->quoteService->getCategories(),
        ]);
    }

    public function update(UpdateQuoteRequest $request, Quote $quote): RedirectResponse
    {
        try {
            $this->quoteService->update($quote, $request->validated());
        } catch (\RuntimeException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->route('quotes.index')
            ->with('success', 'Devis mis à jour avec succès.');
    }

    public function destroy(Quote $quote): RedirectResponse
    {
        try {
            $this->quoteService->delete($quote);
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('quotes.index')
            ->with('success', 'Devis supprimé avec succès.');
    }

    /**
     * Convertit un devis accepté en vente brouillon, puis redirige vers la
     * fiche de cette vente pour finalisation (mode de paiement, garantie,
     * validation) — pas de nouvelle UI de vente à construire.
     */
    public function convert(Quote $quote): RedirectResponse
    {
        try {
            $sale = $this->quoteService->convertToSale($quote, auth()->id());
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('sales.edit', $sale)
            ->with('success', "Devis converti en vente {$sale->sale_number} (brouillon) — vérifiez les détails avant de valider.");
    }

    public function print(Quote $quote): View
    {
        $quote->load(['customer', 'items.product']);
        $downloadUrl = route('quotes.download', $quote);

        return view('documents.quote_document', compact('quote', 'downloadUrl'));
    }

    public function download(Quote $quote): Response
    {
        $content = $this->quoteService->renderPdfContent($quote);
        $fileName = "{$quote->quote_number}.pdf";

        return response($content, 200, [
            'Content-Type' => 'application/pdf; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ]);
    }

    /**
     * Version publique (lien signé, sans authentification) du devis —
     * utilisée par le lien partagé sur WhatsApp pour que le client puisse
     * ouvrir directement le document sans être connecté à l'application.
     */
    public function publicPdf(Quote $quote): Response
    {
        $content = $this->quoteService->renderPdfContent($quote);
        $fileName = "{$quote->quote_number}.pdf";

        return response($content, 200, [
            'Content-Type' => 'application/pdf; charset=UTF-8',
            'Content-Disposition' => "inline; filename=\"{$fileName}\"",
        ]);
    }

    public function sendWhatsApp(Quote $quote): RedirectResponse
    {
        $built = $this->buildWhatsAppPayload($quote);

        if ($built['waUrl'] === null) {
            return back()->with('error', "Le client n'a pas de numéro WhatsApp valide (format sénégalais +221 attendu).");
        }

        return redirect()->away($built['waUrl']);
    }

    public function whatsAppPayload(Quote $quote): JsonResponse
    {
        $built = $this->buildWhatsAppPayload($quote);

        if ($built['waUrl'] === null) {
            return response()->json([
                'error' => "Le client n'a pas de numéro WhatsApp valide (format sénégalais +221 attendu).",
            ], 422);
        }

        return response()->json($built);
    }

    private function buildWhatsAppPayload(Quote $quote): array
    {
        $quote->load('customer');

        $pdfUrl = URL::signedRoute('quotes.public-pdf', ['quote' => $quote->id]);
        $message = $this->whatsAppService->buildQuoteMessage($quote, $pdfUrl);
        $waUrl = $this->whatsAppService->buildLink($quote->customer?->phone, $message);

        return [
            'message' => $message,
            'pdfUrl' => $pdfUrl,
            'waUrl' => $waUrl,
            'fileName' => $quote->quote_number . '.pdf',
        ];
    }
}
