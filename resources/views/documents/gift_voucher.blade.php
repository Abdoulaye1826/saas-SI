<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $entreprise->name }} — Bon de cadeau {{ $gift->gift_number }}</title>
  <style>
    /* Gabarit délibérément distinct de documents/sale_document.blade.php :
       un cadeau n'est pas une facture (cahier §7) — aucune section
       total/paiement/reste à payer/mode de paiement n'existe ici, même en
       tant que classe CSS inutilisée. */
    * { margin: 0; padding: 0; box-sizing: border-box; }

    @page { margin: 0; }

    :root {
      --accent: {{ $entreprise->accent_color ?: '#1e3a5f' }};
      --accent-dark: {{ $entreprise->accent_color_dark }};
      --text: #1a1a2e;
      --text-muted: #5b6479;
      --line: #c7cad6;
      --line-light: #e3e5ec;
      --band-bg: #f5f6f8;
    }

    body { font-family: 'Segoe UI', Arial, sans-serif; font-size: 13px; color: var(--text); background: #fff; }

    .page { width: 210mm; margin: 0 auto; background: #fff; position: relative; }

    .top-stripe { height: 7px; background: var(--accent); }

    .header-inner { display: table; width: 100%; padding: 22px 32px 16px; }
    .brand { display: table-cell; vertical-align: top; }
    .brand-row { display: flex; align-items: center; gap: 14px; }
    .brand-icon { width: 70px; height: 70px; border-radius: 18px; display: flex; align-items: center; justify-content: center; border: 2px solid var(--accent); overflow: hidden; }
    .brand-icon img { width: 100%; height: 100%; object-fit: cover; display: block; }
    .brand-name { color: var(--text); font-size: 20px; font-weight: 700; letter-spacing: -0.3px; line-height: 1; }
    .brand-sub { color: var(--text-muted); font-size: 9.5px; letter-spacing: 2px; text-transform: uppercase; margin-top: 3px; }

    .header-doc { display: table-cell; vertical-align: top; text-align: right; }
    .doc-type { color: var(--accent); font-size: 12px; font-weight: 700; letter-spacing: 3px; text-transform: uppercase; margin-bottom: 6px; }
    .doc-number { color: var(--text); font-size: 24px; font-weight: 700; letter-spacing: -0.3px; }
    .doc-date { color: var(--text-muted); font-size: 11px; margin-top: 4px; }

    .meta-band { display: table; table-layout: fixed; width: 100%; padding: 16px 32px; background: var(--band-bg); }
    .meta-block { display: table-cell; vertical-align: top; width: 55%; }
    .meta-block h4 { font-size: 10px; font-weight: 700; letter-spacing: 1.5px; text-transform: uppercase; color: var(--accent); margin-bottom: 8px; }
    .meta-block p { color: var(--text); font-size: 13px; line-height: 1.6; }
    .meta-block .name { font-size: 14px; font-weight: 700; color: var(--text); text-transform: uppercase; }
    .meta-block.right { width: 45%; }
    .meta-row { display: table; width: 100%; margin-bottom: 8px; }
    .meta-row:last-child { margin-bottom: 0; }
    .meta-row .meta-row-label { display: table-cell; font-size: 10px; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; color: var(--text-muted); vertical-align: middle; }
    .meta-row .meta-row-value { display: table-cell; text-align: right; font-size: 13px; font-weight: 700; color: var(--text); vertical-align: middle; }

    /* ── BANDEAU « PRODUIT OFFERT — AUCUN MONTANT À PAYER » (cahier §7,
       texte exact) : la pièce centrale du document, impossible à manquer. ── */
    .gift-banner {
      margin: 18px 32px 0; padding: 14px 18px; border-radius: 6px;
      background: color-mix(in srgb, var(--accent) 12%, white);
      border: 1px solid var(--accent);
      display: flex; align-items: center; gap: 12px;
    }
    .gift-banner .icon { font-size: 22px; }
    .gift-banner .label { font-size: 14px; font-weight: 700; letter-spacing: 0.5px; color: var(--accent-dark); text-transform: uppercase; }

    .items-section { padding: 18px 32px 0; }
    .items-table { width: 100%; border-collapse: collapse; }
    .items-table thead th {
      background: {{ $entreprise->accent_color_dark }}; color: #fff;
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
    .items-table tbody td.amount { text-align: right; color: var(--text-muted); }

    /* Valeur indicative : affichée à titre informatif uniquement (cahier
       §6) — jamais un total, jamais un montant à payer. */
    .indicative-note { margin: 10px 32px 0; font-size: 10.5px; color: var(--text-muted); font-style: italic; }

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
      .page { box-shadow: 0 4px 30px rgba(21,59,255,0.10); border-radius: 4px; }
    }
  </style>
  @if(empty($isPdf))
    <style>
      .page { min-height: 297mm; padding-bottom: 140px; }
      .bottom-section { position: absolute; left: 0; right: 0; bottom: 0; margin-top: 0; }
    </style>
  @else
    <style>
      .page { padding-bottom: 130px; }
      .bottom-section { position: fixed; left: 0; right: 0; bottom: 0; margin-top: 0; background: #fff; }
    </style>
  @endif
</head>
<body>

@if(empty($isPdf))
<div class="no-print" style="display:flex;justify-content:center;gap:12px;margin-bottom:16px;">
  <a href="{{ url()->previous() }}" class="btn btn-outline-secondary" style="padding:10px 28px;border-radius:8px;font-size:13px;font-weight:600;">
    🔙 Retour
  </a>
  <button onclick="window.print()" style="background:{{ $entreprise->accent_color ?: '#1e3a5f' }};color:#fff;border:none;padding:10px 28px;border-radius:8px;font-size:13px;cursor:pointer;font-weight:600;">
    🖨️ Imprimer
  </button>
  @if(!empty($downloadUrl))
    <a href="{{ $downloadUrl }}" style="background:{{ $entreprise->accent_color_dark }};color:#fff;border:none;padding:10px 28px;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none;">
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
          <img src="{{ $entreprise->logo_base64 }}" alt="{{ $entreprise->name }}">
        </div>
        <div>
          <div class="brand-name">{{ $entreprise->name }}</div>
          <div class="brand-sub">Système d'information</div>
        </div>
      </div>
    </div>
    <div class="header-doc">
      <div class="doc-type">Bon de cadeau</div>
      <div class="doc-number">#{{ $gift->gift_number }}</div>
      <div class="doc-date">{{ $gift->gift_date->locale('fr')->translatedFormat('d F Y à H:i') }}</div>
    </div>
  </div>

  <div class="meta-band">
    <div class="meta-block">
      <h4>Client bénéficiaire</h4>
      @if($gift->customer)
        <p class="name">{{ $gift->customer->full_name }}</p>
        @if($gift->customer->phone)
          <p>{{ $gift->customer->phone }}</p>
        @endif
      @else
        <p class="name">—</p>
      @endif
    </div>
    <div class="meta-block right">
      <div class="meta-row">
        <span class="meta-row-label">Offert par</span>
        <span class="meta-row-value">{{ $gift->user?->name ?? '—' }}</span>
      </div>
      <div class="meta-row">
        <span class="meta-row-label">Date</span>
        <span class="meta-row-value">{{ $gift->gift_date->locale('fr')->translatedFormat('d M Y') }}</span>
      </div>
    </div>
  </div>

  {{-- Texte exact du cahier §7. --}}
  <div class="gift-banner">
    <span class="icon">🎁</span>
    <span class="label">Produit offert — Aucun montant à payer</span>
  </div>

  <div class="items-section">
    <table class="items-table">
      <thead>
        <tr>
          <th style="width:55%;text-align:left;">Produit offert</th>
          <th class="num" style="width:20%;">Quantité</th>
          <th class="amount" style="width:25%;">Valeur indicative</th>
        </tr>
      </thead>
      <tbody>
        @forelse($gift->items as $item)
          <tr>
            <td class="desc">
              {{ $item->product?->name ?? '—' }}
              @if($item->productImei)
                <small>IMEI : {{ $item->productImei->imei }}</small>
              @endif
            </td>
            <td class="qty">{{ $item->quantity }}</td>
            <td class="amount">{{ number_format($item->unit_value * $item->quantity, 0, ',', ' ') }} F CFA</td>
          </tr>
        @empty
          <tr>
            <td colspan="3" style="text-align:center;padding:30px;color:var(--text-muted);">Aucun produit</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <p class="indicative-note">Valeur affichée à titre indicatif uniquement — ce document ne constitue ni une facture, ni une demande de paiement.</p>

  <div class="bottom-section">
    <div class="conditions-group">
      <h4>Remarque</h4>
      <p>{{ $gift->notes ?: '—' }}</p>
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
