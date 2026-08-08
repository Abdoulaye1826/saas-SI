<?php

use App\Http\Controllers\Admin\EntrepriseController;
use App\Http\Controllers\Admin\OnlineOrderController;
use App\Http\Controllers\Admin\ProductReviewController;
use App\Http\Controllers\Admin\StoreSettingsController;
use App\Http\Controllers\Storefront\Account\OrderController as AccountOrderController;
use App\Http\Controllers\Storefront\Account\ProfileController as AccountProfileController;
use App\Http\Controllers\Storefront\Auth\AuthenticatedCustomerSessionController;
use App\Http\Controllers\Storefront\Auth\CustomerPasswordResetLinkController;
use App\Http\Controllers\Storefront\Auth\NewCustomerPasswordController;
use App\Http\Controllers\Storefront\Auth\RegisteredCustomerController;
use App\Http\Controllers\Storefront\CartController as StorefrontCartController;
use App\Http\Controllers\Storefront\CategoryController as StorefrontCategoryController;
use App\Http\Controllers\Storefront\CheckoutController as StorefrontCheckoutController;
use App\Http\Controllers\Storefront\HomeController as StorefrontHomeController;
use App\Http\Controllers\Storefront\ProductController as StorefrontProductController;
use App\Http\Controllers\Storefront\ReviewController as StorefrontReviewController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ManifestController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductImeiController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReturnController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\TreasuryDashboardController;
use App\Http\Controllers\TreasuryExpenseController;
use App\Http\Controllers\TreasuryHistoryController;
use App\Http\Controllers\TreasuryReportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WarrantyController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Routes Web — Mboup Gaming SI
|--------------------------------------------------------------------------
*/

Route::get('/', fn () => redirect()->route('login'));

// Manifeste web (favicon, icône "Ajouter à l'écran d'accueil") : accessible
// sans authentification, un navigateur peut le récupérer depuis la page de
// connexion elle-même.
Route::get('/manifest.webmanifest', [ManifestController::class, 'index'])->name('manifest');

// ── Documents partagés publiquement (liens signés, sans authentification) ──
// Utilisés par le bouton WhatsApp pour que le client ouvre directement le
// PDF (facture ou bon d'échange) sans être redirigé vers la page de
// connexion. Le middleware "signed" garantit qu'un lien ne peut pas être
// deviné ou modifié pour accéder à un autre document.
Route::get('invoices/{invoice}/public-pdf', [InvoiceController::class, 'publicPdf'])
    ->name('invoices.public-pdf')->middleware('signed');
Route::get('sales/{sale}/exchange-voucher/public-pdf', [SaleController::class, 'publicExchangeVoucherPdf'])
    ->name('sales.exchange-voucher.public-pdf')->middleware('signed');
Route::get('quotes/{quote}/public-pdf', [QuoteController::class, 'publicPdf'])
    ->name('quotes.public-pdf')->middleware('signed');

Route::middleware(['auth', 'active'])->group(function () {

    // ── Dashboard (tous les rôles authentifiés) ─────────────
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ── Profil ────────────────────────────────────────────────
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ── Notifications ─────────────────────────────────────────
    Route::post('/notifications/read-all', [NotificationController::class, 'readAll'])->name('notifications.read-all');
    Route::get('/notifications/{notification}/read', [NotificationController::class, 'read'])->name('notifications.read');

    // ── Keep-alive session (évite l'expiration silencieuse pendant
    //    l'inactivité côté onglet ouvert — voir public/js/session-keepalive.js) ──
    Route::get('/keep-alive', fn () => response()->noContent())->name('keep-alive');

    // ── Produits & Catégories (Admin, Gestionnaire) ───────────
    Route::middleware('role:admin,manager')->group(function () {
        Route::resource('categories', CategoryController::class)->except(['show']);
        Route::resource('products', ProductController::class);
        Route::resource('suppliers', SupplierController::class)->except(['show']);
        Route::get('stock', [StockController::class, 'index'])->name('stock.index');
        Route::post('products/{product}/stock/adjust', [StockController::class, 'adjust'])->name('products.stock.adjust');

        // ── IMEI (téléphones) ────────────────────────────────
        Route::post('products/{product}/imeis', [ProductImeiController::class, 'store'])->name('products.imeis.store');
        Route::delete('imeis/{imei}', [ProductImeiController::class, 'destroy'])->name('imeis.destroy');
    });

    // ── Rapports (Admin, Gestionnaire, Caissier) ──────────────
    Route::middleware('role:admin,manager,cashier')->group(function () {
        Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    });

    // ── Clients, Ventes, Factures (Admin, Gestionnaire, Caissier)
    Route::middleware('role:admin,manager,cashier')->group(function () {
        Route::resource('customers', CustomerController::class)->except(['show']);
        Route::get('sales/customers/search', [SaleController::class, 'searchCustomers'])->name('sales.customers.search');
        Route::get('sales/exchange-products/search', [SaleController::class, 'searchExchangeProducts'])->name('sales.exchange-products.search');
        Route::post('sales/exchange-products/store', [SaleController::class, 'storeExchangeProduct'])->name('sales.exchange-products.store');
        Route::get('products/{product}/available-imeis', [ProductImeiController::class, 'available'])->name('products.imeis.available');
        Route::resource('sales', SaleController::class)->except(['show']);
        Route::get('sales/{sale}/exchange-voucher/print', [SaleController::class, 'printExchangeVoucher'])->name('sales.exchange-voucher.print');
        Route::get('sales/{sale}/exchange-voucher/download', [SaleController::class, 'downloadExchangeVoucher'])->name('sales.exchange-voucher.download');
        Route::get('sales/{sale}/exchange-voucher/whatsapp', [SaleController::class, 'sendExchangeVoucherWhatsApp'])->name('sales.exchange-voucher.whatsapp');
        Route::get('sales/{sale}/exchange-voucher/whatsapp-payload', [SaleController::class, 'exchangeVoucherWhatsAppPayload'])->name('sales.exchange-voucher.whatsapp.payload');
        Route::resource('invoices', InvoiceController::class)->except(['show']);
        Route::get('invoices/{invoice}/print', [InvoiceController::class, 'print'])->name('invoices.print');
        Route::get('invoices/{invoice}/download', [InvoiceController::class, 'download'])->name('invoices.download');
        Route::get('invoices/{invoice}/whatsapp-payload', [InvoiceController::class, 'whatsAppPayload'])->name('invoices.whatsapp.payload');
        Route::get('invoices/{invoice}/whatsapp', [InvoiceController::class, 'sendWhatsApp'])->name('invoices.whatsapp');
        Route::post('invoices/{invoice}/email', [InvoiceController::class, 'sendEmail'])->name('invoices.email');

        // ── Paiements de factures ─────────────────────────────
        Route::post('invoices/{invoice}/payments', [PaymentController::class, 'store'])->name('invoices.payments.store');
        Route::delete('payments/{payment}', [PaymentController::class, 'destroy'])->name('payments.destroy');

        // ── Gestion des retours ──────────────────────────────
        Route::get('returns', [ReturnController::class, 'index'])->name('returns.index');
        Route::post('returns/{saleItem}', [ReturnController::class, 'store'])->name('returns.store');

        // ── Garanties ─────────────────────────────────────────
        Route::get('warranties', [WarrantyController::class, 'index'])->name('warranties.index');

        // ── Devis ─────────────────────────────────────────────
        Route::resource('quotes', QuoteController::class)->except(['show']);
        Route::get('quotes/{quote}/print', [QuoteController::class, 'print'])->name('quotes.print');
        Route::get('quotes/{quote}/download', [QuoteController::class, 'download'])->name('quotes.download');
        Route::get('quotes/{quote}/whatsapp-payload', [QuoteController::class, 'whatsAppPayload'])->name('quotes.whatsapp.payload');
        Route::get('quotes/{quote}/whatsapp', [QuoteController::class, 'sendWhatsApp'])->name('quotes.whatsapp');
        Route::post('quotes/{quote}/convert', [QuoteController::class, 'convert'])->name('quotes.convert');
    });

    // ── Utilisateurs (Admin et Gestionnaire) ─────────────────
    Route::middleware('role:admin,manager')->group(function () {
        Route::resource('users', UserController::class)->except(['show']);
    });

    // ── Trésorerie (Admin, Gestionnaire, Caissier) ────────────
    Route::middleware('role:admin,manager,cashier')->prefix('tresorerie')->name('treasury.')->group(function () {
        Route::get('/', [TreasuryDashboardController::class, 'index'])->name('dashboard');
        Route::get('depenses/nouvelle', [TreasuryExpenseController::class, 'create'])->name('expenses.create');
        Route::post('depenses', [TreasuryExpenseController::class, 'store'])->name('expenses.store');
        Route::get('historique', [TreasuryHistoryController::class, 'index'])->name('history.index');
        Route::get('rapports', [TreasuryReportController::class, 'index'])->name('reports.index');
        Route::get('rapports/pdf', [TreasuryReportController::class, 'pdf'])->name('reports.pdf');
    });

    // ── Informations de l'entreprise (Admin uniquement) ──────
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('entreprise', [EntrepriseController::class, 'edit'])->name('entreprise.edit');
        Route::put('entreprise', [EntrepriseController::class, 'update'])->name('entreprise.update');
    });

    // ── Réglages de la boutique en ligne (Admin, Gestionnaire) ──
    Route::middleware('role:admin,manager')->prefix('boutique-admin/reglages')->name('admin.store.')->group(function () {
        Route::get('general', [StoreSettingsController::class, 'editGeneral'])->name('general.edit');
        Route::put('general', [StoreSettingsController::class, 'updateGeneral'])->name('general.update');
        Route::get('apparence', [StoreSettingsController::class, 'editAppearance'])->name('appearance.edit');
        Route::put('apparence', [StoreSettingsController::class, 'updateAppearance'])->name('appearance.update');
        Route::get('livraison', [StoreSettingsController::class, 'editDelivery'])->name('delivery.edit');
        Route::put('livraison', [StoreSettingsController::class, 'updateDelivery'])->name('delivery.update');
    });

    // ── Commandes en ligne (Admin, Gestionnaire, Caissier) ──────
    Route::middleware('role:admin,manager,cashier')->prefix('commandes-en-ligne')->name('online-orders.')->group(function () {
        Route::get('/', [OnlineOrderController::class, 'index'])->name('index');
        Route::get('{onlineOrder}', [OnlineOrderController::class, 'show'])->name('show');
        Route::post('{onlineOrder}/confirmer', [OnlineOrderController::class, 'confirm'])->name('confirm');
        Route::post('{onlineOrder}/statut', [OnlineOrderController::class, 'updateStatus'])->name('update-status');
        Route::post('{onlineOrder}/annuler', [OnlineOrderController::class, 'cancel'])->name('cancel');
    });

    // ── Avis clients (Admin, Gestionnaire) ──────────────────────
    Route::middleware('role:admin,manager')->prefix('avis-clients')->name('product-reviews.')->group(function () {
        Route::get('/', [ProductReviewController::class, 'index'])->name('index');
        Route::post('{productReview}/valider', [ProductReviewController::class, 'approve'])->name('approve');
        Route::post('{productReview}/refuser', [ProductReviewController::class, 'reject'])->name('reject');
        Route::post('{productReview}/masquer', [ProductReviewController::class, 'hide'])->name('hide');
    });
});

// ── Boutique en ligne (publique, sans authentification) ──────────
// Phase 1 : catalogue en lecture seule (recherche/filtres/détail),
// pas encore de panier ni de commande. Bloquée par EnsureStoreIsOpen
// tant que le statut n'est pas "active" (voir Admin > Boutique > Général).
Route::middleware('store.open')->prefix('boutique')->name('store.')->group(function () {
    Route::get('/', [StorefrontHomeController::class, 'index'])->name('home');
    Route::get('produits', [StorefrontProductController::class, 'index'])->name('products.index');
    Route::get('produits/{product:slug}', [StorefrontProductController::class, 'show'])->name('products.show');
    Route::get('categorie/{category:slug}', [StorefrontCategoryController::class, 'show'])->name('categories.show');

    // ── Avis clients (réservé aux clients connectés) ────────────
    Route::post('produits/{product}/avis', [StorefrontReviewController::class, 'store'])
        ->middleware('auth.customer')->name('reviews.store');

    // ── Panier (session, achat invité) ────────────────────────
    Route::get('panier', [StorefrontCartController::class, 'show'])->name('cart.show');
    Route::post('panier/ajouter', [StorefrontCartController::class, 'add'])->name('cart.add');
    Route::patch('panier/{product}', [StorefrontCartController::class, 'update'])->name('cart.update');
    Route::delete('panier/{product}', [StorefrontCartController::class, 'remove'])->name('cart.remove');

    // ── Checkout invité ou connecté ─────────────────────────────
    Route::get('commande', [StorefrontCheckoutController::class, 'show'])->name('checkout.show');
    Route::post('commande', [StorefrontCheckoutController::class, 'store'])->name('checkout.store');

    // ── Compte client (Phase 3) ─────────────────────────────────
    Route::prefix('compte')->name('account.')->group(function () {
        Route::middleware('guest.customer')->group(function () {
            Route::get('inscription', [RegisteredCustomerController::class, 'create'])->name('register');
            Route::post('inscription', [RegisteredCustomerController::class, 'store']);
            Route::get('connexion', [AuthenticatedCustomerSessionController::class, 'create'])->name('login');
            Route::post('connexion', [AuthenticatedCustomerSessionController::class, 'store']);
            Route::get('mot-de-passe-oublie', [CustomerPasswordResetLinkController::class, 'create'])->name('password.request');
            Route::post('mot-de-passe-oublie', [CustomerPasswordResetLinkController::class, 'store'])->name('password.email');
            Route::get('reinitialiser-mot-de-passe/{token}', [NewCustomerPasswordController::class, 'create'])->name('password.reset');
            Route::post('reinitialiser-mot-de-passe', [NewCustomerPasswordController::class, 'store'])->name('password.store');
        });

        Route::middleware('auth.customer')->group(function () {
            Route::post('deconnexion', [AuthenticatedCustomerSessionController::class, 'destroy'])->name('logout');
            Route::get('profil', [AccountProfileController::class, 'edit'])->name('profile.edit');
            Route::put('profil', [AccountProfileController::class, 'update'])->name('profile.update');
            Route::put('mot-de-passe', [AccountProfileController::class, 'updatePassword'])->name('password.update');
            Route::get('commandes', [AccountOrderController::class, 'index'])->name('orders.index');
            Route::get('commandes/{onlineOrder}', [AccountOrderController::class, 'show'])->name('orders.show');
            Route::get('commandes/{onlineOrder}/facture', [AccountOrderController::class, 'downloadInvoice'])->name('orders.invoice');
        });
    });
});

// Confirmation de commande : lien signé (numéro de commande prévisible,
// jamais suffisant seul pour ouvrir la confirmation d'un autre client).
Route::get('boutique/commande/{order}/confirmation', [StorefrontCheckoutController::class, 'confirmation'])
    ->middleware(['store.open', 'signed'])->name('store.checkout.confirmation');

require __DIR__.'/auth.php';
