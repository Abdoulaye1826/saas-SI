<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Mboup Gaming — Devis {{ $quote->quote_number }}</title>
  <style>
    /* Même gabarit que documents/sale_document.blade.php (voir ce fichier
       pour le détail des choix DomPDF : pas de var() sur <th>/<td>, pas de
       min-height sur .page même dans @media screen). */
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

    /* Couleurs en hex brut (pas de var()) : DomPDF ne résout pas les
       variables CSS sur les vraies balises <th>/<td>. */
    .items-section { padding: 18px 32px 0; }

    .items-table { width: 100%; border-collapse: collapse; }
    .items-table thead th {
      background: #14283f; color: #fff;
      padding: 10px 12px; font-size: 10px; font-weight: 700; letter-spacing: 1px;
      text-transform: uppercase; text-align: left;
    }
    .items-table thead th.num    { text-align: center; background: #4c7ab5; }
    .items-table thead th.amount { text-align: right; }

    .items-table tbody tr { border-bottom: 1px solid #e3e5ec; }
    .items-table tbody td { padding: 12px; color: #1a1a2e; vertical-align: top; }
    .items-table tbody td.desc { font-weight: 700; }
    .items-table tbody td.qty   { text-align: center; }
    .items-table tbody td.unit  { text-align: right; }
    .items-table tbody td.total { text-align: right; font-weight: 700; }

    .totals-row { display: flex; justify-content: flex-end; padding: 14px 32px 0; }
    .totals-box { width: 290px; }

    .totals-line { display: flex; justify-content: space-between; padding: 5px 0; font-size: 13px; color: var(--text-muted); }
    .totals-line .val   { font-weight: 600; color: var(--text); }

    .totals-grand { display: flex; justify-content: space-between; align-items: center; padding: 8px 0 0; margin-top: 4px; }
    .totals-grand .label { font-size: 15px; font-weight: 700; color: var(--text); }
    .totals-grand .val   { font-size: 19px; font-weight: 700; color: var(--text); }

    .amount-words {
      margin: 14px 32px 0; padding: 10px 14px 0; border-top: 1px solid var(--line-light);
      font-size: 11.5px; color: var(--text-muted);
    }
    .amount-words span { font-weight: 700; color: var(--text); }

    /* Un seul bloc pour les conditions et le contact, comme sur le modèle
       de référence : un filet supérieur, pas de filet entre les deux. */
    .bottom-section { margin-top: 22px; padding: 16px 32px 14px; border-top: 1px solid var(--line); }
    .conditions-group h4 { font-size: 11px; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; color: var(--accent); margin-bottom: 6px; }
    .conditions-group p { font-size: 11.5px; color: var(--text-muted); line-height: 1.6; }

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
      .page { box-shadow: 0 4px 30px rgba(30,58,95,0.10); border-radius: 4px; }
    }
  </style>
  @if(empty($isPdf))
    {{-- Voir sale_document.blade.php : réservé à l'aperçu navigateur,
         jamais envoyé à DomPDF. --}}
    <style>
      .page { min-height: 297mm; padding-bottom: 160px; }
      .bottom-section { position: absolute; left: 0; right: 0; bottom: 0; margin-top: 0; }
    </style>
  @endif
</head>
<body>

@php
  // Voir sale_document.blade.php : DomPDF ne charge pas les images
  // distantes par défaut, donc le logo restait vide dans le PDF envoyé/
  // téléchargé. Le data URI base64 fonctionne à l'identique dans l'aperçu
  // navigateur et dans le PDF généré par DomPDF.
  $logoPath = public_path('images/logo.jpeg');
  $logoSrc = is_file($logoPath)
      ? 'data:image/jpeg;base64,' . base64_encode(file_get_contents($logoPath))
      : asset('images/logo.jpeg');
@endphp

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

  <div class="header-inner">
    <div class="brand">
      <div class="brand-row">
        <div class="brand-icon">
          <img src="{{ $logoSrc }}" alt="Mboup Gaming">
        </div>
        <div>
          <div class="brand-name">Mboup Gaming</div>
          <div class="brand-sub">Système d'information</div>
        </div>
      </div>
    </div>
    <div class="header-doc">
      <div class="doc-type">Devis</div>
      <div class="doc-number">#{{ $quote->quote_number }}</div>
      <div class="doc-date">{{ $quote->quote_date->locale('fr')->translatedFormat('d F Y') }}</div>
    </div>
  </div>

  <div class="meta-band">
    <div class="meta-block">
      <h4>Client</h4>
      @if($quote->customer)
        <p class="name">{{ $quote->customer->full_name }}</p>
        @if($quote->customer->address)<p>{{ $quote->customer->address }}</p>@endif
        @if($quote->customer->phone)<p>{{ $quote->customer->phone }}</p>@endif
        @if($quote->customer->email)<p>{{ $quote->customer->email }}</p>@endif
      @else
        <p class="name">Client anonyme</p>
      @endif
    </div>

    <div class="meta-block right">
      <div class="meta-row">
        <span class="meta-row-label">Date</span>
        <span class="meta-row-value">{{ $quote->quote_date->locale('fr')->translatedFormat('d M Y') }}</span>
      </div>
      @if($quote->valid_until)
        <div class="meta-row">
          <span class="meta-row-label">Valable jusqu'au</span>
          <span class="meta-row-value">{{ $quote->valid_until->format('d/m/Y') }}</span>
        </div>
      @endif
      <div class="meta-row">
        <span class="meta-row-label">Statut</span>
        <span class="meta-row-value"><span class="status-pill">{{ $quote->status->label() }}</span></span>
      </div>
    </div>
  </div>

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
        @forelse($quote->items as $item)
          <tr>
            <td class="desc">{{ $item->product?->name ?? '—' }}</td>
            <td class="qty">{{ $item->quantity }}</td>
            <td class="unit">{{ number_format($item->unit_price, 0, ',', ' ') }} F CFA</td>
            <td class="total">{{ number_format($item->line_total, 0, ',', ' ') }} F CFA</td>
          </tr>
        @empty
          <tr>
            <td colspan="4" style="text-align:center;padding:30px;color:var(--text-muted);">Aucun article</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="totals-row">
    <div class="totals-box">
      @php
        $discount = (float) ($quote->discount_amount ?? 0);
        $total = (float) $quote->total_ttc;
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
        <span class="label">Total Devis</span>
        <span class="val">{{ number_format($total, 0, ',', ' ') }} F CFA</span>
      </div>
    </div>
  </div>

  <div class="amount-words">
    Devis établi pour la somme de : <span>{{ \App\Helpers\NumberHelper::toWords($total) ?? number_format($total, 0, ',', ' ') . ' Francs CFA' }}</span>
  </div>

  <div class="bottom-section">
    <div class="conditions-group">
      <h4>Conditions</h4>
      <p>
        @if($quote->notes)
          {{ $quote->notes }}
        @else
          @if($quote->valid_until)
            Ce devis est valable jusqu'au {{ $quote->valid_until->format('d/m/Y') }}. Les prix indiqués peuvent varier après cette date.
          @else
            Les prix indiqués sont susceptibles de varier dans le temps.
          @endif
          Ce document ne constitue pas une facture et n'engage pas de commande tant qu'il n'a pas été accepté.
        @endif
      </p>
    </div>

    <div class="footer-line">
      Tél : <strong>{{ config('company.phone') }}</strong>
      &nbsp;&nbsp;·&nbsp;&nbsp;Email : {{ config('company.email') }}
      &nbsp;&nbsp;·&nbsp;&nbsp;{{ config('company.address_line1') }}, {{ config('company.address_line2') }}
    </div>
    <div class="footer-legal">
      Ninea : {{ config('company.ninea') }} — RC : {{ config('company.rc') }}
    </div>
  </div>

</div>

</body>
</html>
