<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  @php
    $isEchange = $sale->isEchange();
    $documentType = $isEchange ? "Bon d'échange" : 'Facture';
    $documentNumber = $isEchange ? $sale->exchange_voucher_number : ($invoice->invoice_number ?? $sale->sale_number);
  @endphp
  <title>{{ $entreprise->name }} — {{ $documentType }} {{ $documentNumber }}</title>
  <style>
    /* ============================================================
       Gabarit calqué sur le modèle de référence (bandeau d'accent,
       bloc client sur fond gris clair, tableau à en-tête sombre,
       conditions en pied de page) — même structure, en bleu marine
       (charte Mboup Gaming) plutôt que doré.
       ============================================================ */
    * { margin: 0; padding: 0; box-sizing: border-box; }

    @page {
      margin: 0;
    }

    :root {
      --accent: #1e3a5f;
      --accent-dark: #14283f;
      --accent-light: #4c7ab5;
      --text: #1a1a2e;
      --text-muted: #5b6479;
      --line: #c7cad6;
      --line-light: #e3e5ec;
      --band-bg: #f5f6f8;
    }

    body {
      font-family: 'Segoe UI', Arial, sans-serif;
      font-size: 13px;
      color: var(--text);
      background: #fff;
    }

    .page {
      width: 210mm;
      margin: 0 auto;
      background: #fff;
      position: relative;
    }

    .top-stripe { height: 7px; background: var(--accent); }

    /* ── HEADER ── */
    .header-inner {
      display: table;
      width: 100%;
      padding: 22px 32px 16px;
    }

    .brand { display: table-cell; vertical-align: top; }
    .brand-row { display: flex; align-items: center; gap: 14px; }

    .brand-icon {
      width: 70px; height: 70px;
      border-radius: 50%;
      display: flex; align-items: center; justify-content: center;
      border: 2px solid var(--accent);
      overflow: hidden;
    }

    .brand-icon img { width: 100%; height: 100%; object-fit: cover; display: block; }

    .brand-name { color: var(--text); font-size: 20px; font-weight: 700; letter-spacing: -0.3px; line-height: 1; }
    .brand-sub { color: var(--text-muted); font-size: 9.5px; letter-spacing: 2px; text-transform: uppercase; margin-top: 3px; }

    .header-doc { display: table-cell; vertical-align: top; text-align: right; }
    .doc-type { color: var(--accent); font-size: 12px; font-weight: 700; letter-spacing: 3px; text-transform: uppercase; margin-bottom: 6px; }
    .doc-number { color: var(--text); font-size: 24px; font-weight: 700; letter-spacing: -0.3px; }
    .doc-date { color: var(--text-muted); font-size: 11px; margin-top: 4px; }

    /* ── META BAND : fond gris clair, deux colonnes ──
       display:table (et non grid : mal supporté par DomPDF, cause un
       décalage vertical entre les colonnes). */
    .meta-band {
      display: table;
      table-layout: fixed;
      width: 100%;
      padding: 16px 32px;
      background: var(--band-bg);
    }

    .meta-block { display: table-cell; vertical-align: top; width: 55%; }
    .meta-block h4 { font-size: 10px; font-weight: 700; letter-spacing: 1.5px; text-transform: uppercase; color: var(--accent); margin-bottom: 8px; }
    .meta-block p { color: var(--text); font-size: 13px; line-height: 1.6; }
    .meta-block .name { font-size: 14px; font-weight: 700; color: var(--text); text-transform: uppercase; }

    .meta-block.right { width: 45%; }
    .meta-row { display: table; width: 100%; margin-bottom: 8px; }
    .meta-row:last-child { margin-bottom: 0; }
    .meta-row .meta-row-label { display: table-cell; font-size: 10px; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; color: var(--text-muted); vertical-align: middle; }
    .meta-row .meta-row-value { display: table-cell; text-align: right; font-size: 13px; font-weight: 700; color: var(--text); vertical-align: middle; }

    .status-pill {
      display: inline-block; padding: 4px 14px; border-radius: 4px;
      font-size: 10px; font-weight: 700; letter-spacing: 1px; text-transform: uppercase;
      background: var(--accent); color: #fff;
    }

    /* ── ITEMS TABLE — en-tête sombre, colonne quantité surlignée ── */
    .items-section { padding: 18px 32px 0; }

    /* Couleurs en hex brut (pas de var()) dans ce bloc : DomPDF ne résout
       pas les variables CSS sur les vraies balises <th>/<td> (testé et
       confirmé — le fond restait invisible), alors qu'il le fait très bien
       sur les <div> en display:table-cell utilisées ailleurs dans ce
       document. */
    .items-table { width: 100%; border-collapse: collapse; }
    .items-table thead th {
      background: #14283f; color: #fff;
      padding: 10px 12px; font-size: 10px; font-weight: 700; letter-spacing: 1px;
      text-transform: uppercase; text-align: left;
    }
    .items-table thead th.num    { text-align: center; }
    .items-table thead th.amount { text-align: right; }

    .items-table tbody tr { border-bottom: 1px solid #e3e5ec; }
    .items-table tbody td { padding: 12px; color: #1a1a2e; vertical-align: top; }
    .items-table tbody td.desc { font-weight: 700; }
    .items-table tbody td.desc small { display: block; font-size: 11px; color: #5b6479; font-weight: 400; margin-top: 2px; }
    .items-table tbody td.qty   { text-align: center; }
    .items-table tbody td.unit  { text-align: right; }
    .items-table tbody td.total { text-align: right; font-weight: 700; }

    /* ── ÉCHANGE : PRODUITS — cartes en simple encadré ── */
    .exchange-section { padding: 24px 32px 0; display: grid; grid-template-columns: 1fr auto 1fr; gap: 16px; align-items: stretch; }

    .exchange-card { border: 1px solid var(--line); border-radius: 6px; padding: 14px; }
    .exchange-card h4 { font-size: 10px; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; color: var(--accent); margin-bottom: 8px; }
    .exchange-card .product-name { font-size: 14px; font-weight: 700; color: var(--text); margin-bottom: 4px; }
    .exchange-card .product-ref { font-size: 11px; color: var(--text-muted); margin-bottom: 10px; }
    .exchange-card .value-row { display: flex; justify-content: space-between; align-items: center; border-top: 1px solid var(--line-light); padding-top: 8px; }
    .exchange-card .value-row .label { font-size: 11px; color: var(--text-muted); font-weight: 500; }
    .exchange-card .value-row .val   { font-size: 15px; font-weight: 700; color: var(--text); }

    .exchange-arrow { display: flex; align-items: center; justify-content: center; font-size: 22px; color: var(--text); font-weight: 700; }

    .items-list { margin-top: 8px; }
    .items-list .item-row { display: flex; justify-content: space-between; font-size: 12px; color: var(--text); padding: 2px 0; }
    .items-list .item-row .qty { color: var(--text-muted); }

    /* ── TOTAUX — plats, sans encadré, alignés à droite ── */
    .totals-row { display: flex; justify-content: flex-end; padding: 14px 32px 0; }
    .totals-box { width: 290px; }

    .totals-line { display: flex; justify-content: space-between; padding: 5px 0; font-size: 13px; color: var(--text-muted); }
    .totals-line .val   { font-weight: 600; color: var(--text); }

    .totals-grand { display: flex; justify-content: space-between; align-items: center; padding: 8px 0 0; margin-top: 4px; }
    .totals-grand .label { font-size: 15px; font-weight: 700; color: var(--text); }
    .totals-grand .val   { font-size: 19px; font-weight: 700; color: var(--text); }

    /* ── MONTANT EN LETTRES ── */
    .amount-words {
      margin: 14px 32px 0; padding: 10px 14px 0; border-top: 1px solid var(--line-light);
      font-size: 11.5px; color: var(--text-muted);
    }
    .amount-words span { font-weight: 700; color: var(--text); }

    /* ── BLOC BAS DE PAGE — un seul encadré : garantie + conditions puis,
       juste en dessous (sans filet supplémentaire), la ligne de contact.
       Un seul filet supérieur pour tout le bloc, comme sur le modèle de
       référence (conditions de paiement + contact réunis). ── */
    .bottom-section { margin-top: 22px; padding: 16px 32px 14px; border-top: 1px solid var(--line); }

    .conditions-group + .conditions-group { margin-top: 12px; }
    .conditions-group h4 { font-size: 11px; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; color: var(--accent); margin-bottom: 6px; }
    .conditions-group p { font-size: 11.5px; color: var(--text-muted); line-height: 1.6; }
    .conditions-group p strong { color: var(--text); }

    .footer-line { margin-top: 16px; font-size: 11.5px; color: var(--text-muted); text-align: center; }
    .footer-line strong { color: var(--text); font-weight: 700; }
    .footer-legal { font-size: 9.5px; color: var(--text-muted); margin-top: 4px; text-align: center; }

    @media print {
      html, body { margin: 0; padding: 0; background: #fff; }
      .page { width: 100%; min-height: 100vh; margin: 0; box-shadow: none; border-radius: 0; }
      .no-print { display: none !important; }
    }

    @media screen {
      body { padding: 20px 0 40px; background: #f0f1f4; }
      .page { box-shadow: 0 4px 30px rgba(21,59,255,0.10); border-radius: 4px; }
    }
  </style>
  @if(empty($isPdf))
    {{-- Réservé à l'aperçu navigateur — jamais envoyé à DomPDF, qui gère
         mal min-height et position:absolute sur un pied de page (page
         vide en trop constatée précédemment). Un vrai navigateur, lui,
         n'a pas ce problème de pagination : le footer peut donc être
         collé au bas de .page sans risque ici. --}}
    <style>
      .page { min-height: 297mm; padding-bottom: 160px; }
      .bottom-section { position: absolute; left: 0; right: 0; bottom: 0; margin-top: 0; }
    </style>
  @endif
</head>
<body>

@if(empty($isPdf))
<div class="no-print" style="display:flex;justify-content:center;gap:12px;margin-bottom:16px;">
  <a href="{{ url()->previous() }}" class="btn btn-outline-secondary" style="padding:10px 28px;border-radius:8px;font-size:13px;font-weight:600;">
    🔙 Retour
  </a>
  <button onclick="window.print()" style="background:#1e3a5f;color:#fff;border:none;padding:10px 28px;border-radius:8px;font-size:13px;cursor:pointer;font-weight:600;">
    🖨️ Imprimer
  </button>
  @if(!empty($downloadUrl))
    <a href="{{ $downloadUrl }}" style="background:#14283f;color:#fff;border:none;padding:10px 28px;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none;">
      ⬇️ Télécharger PDF
    </a>
  @endif
</div>
@endif

<div class="page">
  <div class="top-stripe"></div>

  {{-- ── EN-TÊTE ── --}}
  <div class="header-inner">
    <div class="brand">
      <div class="brand-row">
        <div class="brand-icon">
          <img src="{{ $entreprise->logo_base64 }}" alt="{{ $entreprise->name }}">
        </div>
        <div>
          <div class="brand-name">{{ $entreprise->name }}</div>
          <div class="brand-sub">Système d'information</div>
        </div>
      </div>
    </div>
    <div class="header-doc">
      <div class="doc-type">{{ $documentType }}</div>
      <div class="doc-number">#{{ $documentNumber }}</div>
      @php $headerDate = $isEchange ? $sale->sale_date : ($invoice->issued_at ?? $sale->sale_date); @endphp
      <div class="doc-date">{{ $headerDate->locale('fr')->translatedFormat('d F Y') }}</div>
    </div>
  </div>

  {{-- ── MÉTA : Client / Dates ── --}}
  <div class="meta-band">
    <div class="meta-block">
      <h4>{{ $isEchange ? 'Client' : 'Facturé à' }}</h4>
      @if($sale->customer)
        <p class="name">{{ $sale->customer->full_name }}</p>
        @if(!$isEchange && $sale->customer->address)
          <p>{{ $sale->customer->address }}</p>
        @endif
        @if($sale->customer->phone)
          <p>{{ $sale->customer->phone }}</p>
        @endif
        @if(!$isEchange && $sale->customer->email)
          <p>{{ $sale->customer->email }}</p>
        @endif
      @else
        <p class="name">Client anonyme</p>
      @endif
    </div>

    <div class="meta-block right">
      @php $metaDate = $isEchange ? $sale->sale_date : ($invoice->issued_at ?? $sale->sale_date); @endphp
      <div class="meta-row">
        <span class="meta-row-label">Date</span>
        <span class="meta-row-value">{{ $metaDate->locale('fr')->translatedFormat('d M Y') }}</span>
      </div>
      @if(!$isEchange)
        <div class="meta-row">
          <span class="meta-row-label">Vente associée</span>
          <span class="meta-row-value">{{ $sale->sale_number }}</span>
        </div>
      @endif
      @if($invoice)
        <div class="meta-row">
          <span class="meta-row-label">Statut</span>
          <span class="meta-row-value"><span class="status-pill">{{ $invoice->status->label() }}</span></span>
        </div>
      @endif
    </div>
  </div>

  @if($isEchange)
    {{-- ── ÉCHANGE : PRODUIT APPORTÉ / PRODUIT REMIS ── --}}
    @php
      $exchangeDetails = $sale->exchange_details ?? [];
      $broughtQuantity = (int) ($exchangeDetails['quantity'] ?? 1);
      $givenQuantity = (int) $sale->items->sum('quantity');
      $addedAmount = (float) ($exchangeDetails['added_amount'] ?? 0);
    @endphp

    <div class="exchange-section">
      <div class="exchange-card">
        <h4>Produit apporté par le client</h4>
        <div class="product-name">{{ $exchangeDetails['name'] ?? '—' }}</div>
        <div class="product-ref">
          {{ $exchangeDetails['reference'] ?? '' }}
          @if(!empty($exchangeDetails['brand'])) — {{ $exchangeDetails['brand'] }} @endif
        </div>
        @if(!empty($exchangeDetails['imei']))
          <div class="product-ref">IMEI : {{ $exchangeDetails['imei'] }}</div>
        @endif
        <div class="value-row">
          <span class="label">Quantité apportée</span>
          <span class="val">{{ $broughtQuantity }}</span>
        </div>
      </div>

      <div class="exchange-arrow">⇄</div>

      <div class="exchange-card">
        <h4>Produit remis par le magasin</h4>
        <div class="items-list">
          @forelse($sale->items as $item)
            <div class="item-row">
              <span>
                {{ $item->product?->name ?? '—' }}
                @if($item->productImei)
                  <br><small>IMEI : {{ $item->productImei->imei }}</small>
                @endif
                @if($sale->warranty_duration && $sale->warranty_duration->value !== 'none')
                  <br><small>
                    Garantie : {{ $sale->warranty_duration->label() }}
                    @if($sale->warranty_end_date)
                      — valable jusqu'au {{ $sale->warranty_end_date->format('d/m/Y') }}
                    @endif
                  </small>
                @endif
              </span>
              <span class="qty">x{{ $item->quantity }}</span>
            </div>
          @empty
            <div class="item-row"><span>—</span></div>
          @endforelse
        </div>
        <div class="value-row">
          <span class="label">Quantité remise</span>
          <span class="val">{{ $givenQuantity }}</span>
        </div>
      </div>
    </div>

    {{-- ── MONTANT AJOUTÉ : seul montant financier affiché, au même emplacement
         et avec le même style que le « Total final » des factures de vente ── --}}
    <div class="totals-row">
      <div class="totals-box">
        <div class="totals-grand">
          <span class="label">Montant ajouté</span>
          <span class="val">{{ number_format($addedAmount, 0, ',', ' ') }} FCFA</span>
        </div>
        @if($invoice && !$invoice->isFullyPaid())
          <div class="totals-line">
            <span class="label">Payé</span>
            <span class="val">{{ number_format($invoice->amount_paid, 0, ',', ' ') }} FCFA</span>
          </div>
          <div class="totals-line">
            <span class="label">Reste à payer</span>
            <span class="val">{{ number_format($invoice->remaining_amount, 0, ',', ' ') }} FCFA</span>
          </div>
        @endif
      </div>
    </div>
  @else
    {{-- ── VENTE : TABLEAU DES ARTICLES ── --}}
    <div class="items-section">
      <table class="items-table">
        <thead>
          <tr>
            <th style="width:50%;text-align:left;">Désignation</th>
            <th class="num" style="width:15%;">Quantité</th>
            <th class="amount" style="width:17%;">P.U.</th>
            <th class="amount" style="width:18%;">Total</th>
          </tr>
        </thead>
        <tbody>
          @forelse($sale->items as $item)
            <tr>
              <td class="desc">
                {{ $item->product?->name ?? '—' }}
                @if($item->productImei)
                  <small>S/N : {{ $item->productImei->imei }}</small>
                @endif
                @if($sale->warranty_duration && $sale->warranty_duration->value !== 'none')
                  <small>
                    Garantie : {{ $sale->warranty_duration->label() }}
                    @if($sale->warranty_end_date)
                      — valable jusqu'au {{ $sale->warranty_end_date->format('d/m/Y') }}
                    @endif
                  </small>
                @endif
              </td>
              <td class="qty">{{ $item->quantity }}</td>
              <td class="unit">{{ number_format($item->unit_price, 0, ',', ' ') }} F CFA</td>
              <td class="total">{{ number_format($item->line_total ?? ($item->quantity * $item->unit_price), 0, ',', ' ') }} F CFA</td>
            </tr>
          @empty
            <tr>
              <td colspan="4" style="text-align:center;padding:30px;color:var(--text-muted);">Aucun article</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    {{-- ── TOTAUX ── --}}
    <div class="totals-row">
      <div class="totals-box">
        @php
          $discount = (float) ($sale->discount_amount ?? 0);
          $total = (float) $sale->total_ttc;
          $subtotal = $total + $discount;
        @endphp
        <div class="totals-line">
          <span class="label">Sous-total</span>
          <span class="val">{{ number_format($subtotal, 0, ',', ' ') }} F CFA</span>
        </div>
        @if($discount > 0)
          <div class="totals-line">
            <span class="label">Remise</span>
            <span class="val">-{{ number_format($discount, 0, ',', ' ') }} F CFA</span>
          </div>
        @endif
        <div class="totals-grand">
          <span class="label">Total Net</span>
          <span class="val">{{ number_format($total, 0, ',', ' ') }} F CFA</span>
        </div>
        @if($invoice && !$invoice->isFullyPaid())
          <div class="totals-line">
            <span class="label">Payé</span>
            <span class="val">{{ number_format($invoice->amount_paid, 0, ',', ' ') }} F CFA</span>
          </div>
          <div class="totals-line">
            <span class="label">Reste à payer</span>
            <span class="val">{{ number_format($invoice->remaining_amount, 0, ',', ' ') }} F CFA</span>
          </div>
        @endif
      </div>
    </div>

    {{-- ── MONTANT EN LETTRES ── --}}
    <div class="amount-words">
      Arrêtée la présente facture à la somme de : <span>{{ \App\Helpers\NumberHelper::toWords($total) ?? number_format($total, 0, ',', ' ') . ' Francs CFA' }}</span>
    </div>
  @endif

  {{-- ── CONDITIONS + CONTACT ── un seul bloc, comme sur le modèle de
       référence. La garantie est affichée sous chaque produit dans le
       tableau des articles (ou la liste des produits remis pour un
       échange), plus au bon endroit ici. --}}
  <div class="bottom-section">
    <div class="conditions-group">
      <h4>Conditions</h4>
      <p>
        @php $remarksText = $invoice?->notes ?? $sale->notes; @endphp
        @if($remarksText)
          {{ $remarksText }}
        @elseif($entreprise->invoice_footer_note)
          {{ $entreprise->invoice_footer_note }}
        @else
          Le service après-vente peut durer une semaine maximum si la garantie n'est pas expiré. Nous ne remboursons pas — nous réparons ou remplaçons.
        @endif
      </p>
    </div>

    <div class="footer-line">
      Tél : <strong>{{ $entreprise->phone }}</strong>
      &nbsp;&nbsp;·&nbsp;&nbsp;Email : {{ $entreprise->email }}
      &nbsp;&nbsp;·&nbsp;&nbsp;{{ $entreprise->address_line1 }}, {{ $entreprise->address_line2 }}
    </div>
    <div class="footer-legal">
      Ninea : {{ $entreprise->ninea }} — RC : {{ $entreprise->rccm }}
    </div>
  </div>

</div>

</body>
</html>
