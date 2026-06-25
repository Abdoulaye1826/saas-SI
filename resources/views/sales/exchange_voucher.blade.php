<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SEN SOLUTION ELECTRONIQUE — Bon d'échange {{ $sale->exchange_voucher_number }}</title>
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
      font-family: 'Segoe UI', Arial, sans-serif;
      font-size: 13px;
      color: #1a1a2e;
      background: #f0f4f8;
    }

    .page {
      width: 210mm;
      min-height: 297mm;
      margin: 0 auto;
      background: #fff;
      position: relative;
      overflow: hidden;
    }

    .header {
      background: linear-gradient(135deg, #1a237e 0%, #283593 60%, #1565c0 100%);
      padding: 24px 32px 20px;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    .brand { display: flex; align-items: center; gap: 14px; }

    .brand-icon {
      width: 64px; height: 64px;
      background: rgba(255,255,255,0.15);
      border-radius: 50%;
      display: flex; align-items: center; justify-content: center;
      border: 1.5px solid rgba(255,255,255,0.25);
      overflow: hidden;
    }

    .brand-icon img { width: 100%; height: 100%; object-fit: cover; display: block; }

    .brand-name { color: #fff; font-size: 26px; font-weight: 700; letter-spacing: -0.5px; line-height: 1; }
    .brand-sub { color: rgba(255,255,255,0.7); font-size: 11px; letter-spacing: 2px; text-transform: uppercase; margin-top: 3px; }

    .header-doc { text-align: right; }
    .doc-type { color: rgba(255,255,255,0.85); font-size: 11px; letter-spacing: 3px; text-transform: uppercase; margin-bottom: 4px; }
    .doc-number { color: #fff; font-size: 22px; font-weight: 700; letter-spacing: -0.5px; }

    .header-stripe { height: 6px; background: linear-gradient(90deg, #FF6F00, #FFB300, #FFF176, #FFB300, #FF6F00); }

    .meta-band {
      display: grid;
      grid-template-columns: 1fr auto 1fr;
      align-items: start;
      gap: 20px;
      padding: 22px 32px;
      border-bottom: 1px solid #e8eaf6;
      background: #fafbff;
    }

    .meta-block h4 { font-size: 10px; font-weight: 600; letter-spacing: 1.5px; text-transform: uppercase; color: #7986cb; margin-bottom: 8px; }
    .meta-block p { color: #1a237e; font-size: 13px; line-height: 1.7; }
    .meta-block .name { font-size: 15px; font-weight: 600; color: #0d1b6e; }
    .meta-divider { width: 1px; background: #e8eaf6; align-self: stretch; }
    .meta-block.right { text-align: right; }

    .date-badge {
      display: inline-flex; flex-direction: column; align-items: center;
      background: #1a237e; color: #fff; border-radius: 12px; padding: 10px 16px; min-width: 80px;
    }
    .date-badge .day { font-size: 24px; font-weight: 700; line-height: 1; }
    .date-badge .month { font-size: 10px; text-transform: uppercase; letter-spacing: 1px; opacity: 0.8; }
    .date-badge .year { font-size: 12px; opacity: 0.7; }

    .exchange-section { padding: 24px 32px 8px; display: grid; grid-template-columns: 1fr auto 1fr; gap: 16px; align-items: stretch; }

    .exchange-card {
      border: 1px solid #e8eaf6;
      border-radius: 10px;
      padding: 16px;
      background: #f8f9ff;
    }

    .exchange-card h4 { font-size: 11px; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; color: #7986cb; margin-bottom: 10px; }
    .exchange-card .product-name { font-size: 15px; font-weight: 600; color: #1a237e; margin-bottom: 4px; }
    .exchange-card .product-ref { font-size: 11px; color: #9fa8da; margin-bottom: 12px; }
    .exchange-card .value-row { display: flex; justify-content: space-between; align-items: center; border-top: 1px dashed #e0e4f4; padding-top: 10px; }
    .exchange-card .value-row .label { font-size: 12px; color: #4a5580; font-weight: 500; }
    .exchange-card .value-row .val { font-size: 16px; font-weight: 700; color: #1a237e; }

    .exchange-arrow {
      display: flex; align-items: center; justify-content: center;
      font-size: 28px; color: #7986cb; font-weight: 700;
    }

    .items-list { margin-top: 10px; }
    .items-list .item-row { display: flex; justify-content: space-between; font-size: 12px; color: #4a5580; padding: 3px 0; }
    .items-list .item-row .qty { color: #9fa8da; }

    .totals-row { display: flex; justify-content: center; padding: 20px 32px 8px; }
    .totals-box { width: 360px; }

    .totals-grand {
      display: flex; justify-content: space-between; align-items: center;
      background: #1a237e; color: #fff; border-radius: 10px; padding: 14px 18px;
    }
    .totals-grand .label { font-size: 11px; letter-spacing: 1px; text-transform: uppercase; opacity: 0.85; }
    .totals-grand .val { font-size: 20px; font-weight: 700; }

    .validation-section { display: grid; grid-template-columns: 1fr; gap: 16px; padding: 24px 32px; }
    .validation-section .info-card { max-width: 320px; margin: 0 auto; text-align: center; }

    .info-card { background: #f8f9ff; border: 1px solid #e8eaf6; border-radius: 10px; padding: 14px 16px; }
    .info-card h4 { font-size: 10px; font-weight: 600; letter-spacing: 1.5px; text-transform: uppercase; color: #7986cb; margin-bottom: 10px; }
    .info-card p { font-size: 13px; color: #1a237e; font-weight: 600; }

    .signature-box { margin-top: 30px; border-top: 1px dashed #c5cae9; padding-top: 6px; font-size: 11px; color: #9fa8da; text-align: center; }

    .footer {
      margin-top: 8px; background: linear-gradient(135deg, #1a237e 0%, #283593 60%, #1565c0 100%);
      padding: 18px 32px; display: flex; justify-content: space-between; align-items: center;
    }
    .footer-contact { color: rgba(255,255,255,0.9); font-size: 12px; line-height: 1.8; }
    .footer-contact strong { color: #fff; }
    .footer-contact .phone { font-size: 14px; font-weight: 700; }
    .footer-thanks { text-align: right; color: rgba(255,255,255,0.7); font-size: 12px; font-style: italic; }
    .footer-thanks strong { display: block; font-size: 15px; color: #FFD54F; font-style: normal; }

    @media print {
      body { background: #fff; }
      .page { width: 100%; margin: 0; box-shadow: none; }
      .no-print { display: none !important; }
    }

    @media screen {
      body { padding: 20px 0 40px; }
      .page { box-shadow: 0 4px 30px rgba(26,35,126,0.15); border-radius: 4px; }
    }
  </style>
</head>
<body>

<div class="no-print" style="display:flex;justify-content:center;gap:12px;margin-bottom:16px;">
  <a href="{{ url()->previous() }}" class="btn btn-outline-secondary" style="padding:10px 28px;border-radius:8px;font-size:13px;font-weight:600;">
    🔙 Retour
  </a>
  <button onclick="window.print()" style="background:#1a237e;color:#fff;border:none;padding:10px 28px;border-radius:8px;font-size:13px;cursor:pointer;font-weight:600;">
    🖨️ Imprimer
  </button>
  <a href="{{ route('sales.exchange-voucher.download', $sale) }}" class="btn" style="background:#283593;color:#fff;border:none;padding:10px 28px;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none;">
    ⬇️ Télécharger PDF
  </a>
</div>

<div class="page">

  <div class="header">
    <div class="brand">
      <div class="brand-icon">
        <img src="{{ asset('images/logo.png') }}" alt="SEN SOLUTION ELECTRONIQUE">
      </div>
      <div>
        <div class="brand-name">SEN SOLUTION ELECTRONIQUE</div>
        <div class="brand-sub">Système d'information</div>
      </div>
    </div>
    <div class="header-doc">
      <div class="doc-type">Bon d'échange</div>
      <div class="doc-number">{{ $sale->exchange_voucher_number }}</div>
    </div>
  </div>
  <div class="header-stripe"></div>

  <div class="meta-band">
    <div class="meta-block">
      <h4>Client</h4>
      @if($sale->customer)
        <p class="name">{{ $sale->customer->full_name }}</p>
        @if($sale->customer->phone)
          <p>📞 {{ $sale->customer->phone }}</p>
        @endif
      @else
        <p class="name">Client anonyme</p>
      @endif
    </div>

    <div class="meta-divider"></div>

    <div class="meta-block right">
      <h4>Date</h4>
      <div class="date-badge" style="margin-left:auto;">
        <span class="day">{{ $sale->sale_date->format('d') }}</span>
        <span class="month">{{ $sale->sale_date->translatedFormat('M') }}</span>
        <span class="year">{{ $sale->sale_date->format('Y') }}</span>
      </div>
    </div>
  </div>

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
            <span>{{ $item->product?->name ?? '—' }}</span>
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

  <div class="totals-row">
    <div class="totals-box">
      <div class="totals-grand">
        <span class="label">Montant ajouté par le client</span>
        <span class="val">{{ number_format($addedAmount, 0, ',', ' ') }} FCFA</span>
      </div>
    </div>
  </div>

  <div class="validation-section">
    <div class="info-card">
      <h4>Utilisateur ayant effectué l'opération</h4>
      <p>{{ $sale->user?->name ?? '—' }}</p>
      <div class="signature-box">Signature</div>
    </div>
  </div>

  <div class="footer">
    <div class="footer-contact">
      <div class="phone">📞 {{ config('app.company_phone', '+221 XX XXX XX XX') }}</div>
      <strong>{{ config('app.company_name', 'Mon Entreprise') }}</strong><br>
      {{ config('app.company_address', 'Dakar, Sénégal') }}
    </div>
    <div class="footer-thanks">
      <strong>Merci de votre confiance</strong>
      Document généré le {{ now()->format('d/m/Y à H:i') }}
    </div>
  </div>

</div>

</body>
</html>
