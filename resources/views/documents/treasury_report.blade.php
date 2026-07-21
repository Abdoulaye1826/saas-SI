<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>{{ $entreprise->name }} — Rapport de trésorerie</title>
  <style>
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

    .page { width: 210mm; margin: 0 auto; background: #fff; }
    .top-stripe { height: 7px; background: var(--accent); }

    .header-inner { display: table; width: 100%; padding: 22px 32px 16px; }
    .brand { display: table-cell; vertical-align: top; }
    .brand-row { display: flex; align-items: center; gap: 14px; }
    .brand-icon { width: 60px; height: 60px; border-radius: 16px; display: flex; align-items: center; justify-content: center; border: 2px solid var(--accent); overflow: hidden; }
    .brand-icon img { width: 100%; height: 100%; object-fit: cover; display: block; }
    .brand-name { color: var(--text); font-size: 18px; font-weight: 700; }
    .brand-sub { color: var(--text-muted); font-size: 9px; letter-spacing: 2px; text-transform: uppercase; margin-top: 3px; }

    .header-doc { display: table-cell; vertical-align: top; text-align: right; }
    .doc-type { color: var(--accent); font-size: 12px; font-weight: 700; letter-spacing: 3px; text-transform: uppercase; margin-bottom: 6px; }
    .doc-number { color: var(--text); font-size: 18px; font-weight: 700; }
    .doc-date { color: var(--text-muted); font-size: 11px; margin-top: 4px; }

    .summary-band { display: table; table-layout: fixed; width: 100%; padding: 16px 32px; background: var(--band-bg); }
    .summary-cell { display: table-cell; vertical-align: top; width: 33.33%; text-align: center; }
    .summary-cell h4 { font-size: 10px; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; color: var(--text-muted); margin-bottom: 6px; }
    .summary-cell .val { font-size: 18px; font-weight: 700; color: var(--text); }
    .summary-cell.in .val { color: #1b8a5a; }
    .summary-cell.out .val { color: #c0392b; }

    .items-section { padding: 18px 32px 0; }
    .items-table { width: 100%; border-collapse: collapse; }
    .items-table thead th {
      background: {{ $entreprise->accent_color_dark }}; color: #fff;
      padding: 8px 10px; font-size: 9.5px; font-weight: 700; letter-spacing: .5px;
      text-transform: uppercase; text-align: left;
    }
    .items-table thead th.amount { text-align: right; }
    .items-table tbody tr { border-bottom: 1px solid var(--line-light); }
    .items-table tbody td { padding: 8px 10px; color: var(--text); font-size: 11.5px; }
    .items-table tbody td.amount { text-align: right; font-weight: 700; }
    .items-table tbody td.amount.in { color: #1b8a5a; }
    .items-table tbody td.amount.out { color: #c0392b; }
    .type-pill { display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 9px; font-weight: 700; text-transform: uppercase; color: #fff; }
    .item-detail { font-size: 9.5px; color: var(--text-muted); margin-top: 2px; }
    .type-pill.in { background: #1b8a5a; }
    .type-pill.out { background: #c0392b; }

    .footer-line { margin-top: 22px; padding: 14px 32px; border-top: 1px solid var(--line); font-size: 11px; color: var(--text-muted); text-align: center; }
    .footer-line strong { color: var(--text); }
  </style>
</head>
<body>

<div class="page">
  <div class="top-stripe"></div>

  <div class="header-inner">
    <div class="brand">
      <div class="brand-row">
        <div class="brand-icon"><img src="{{ $entreprise->logo_base64 }}" alt="{{ $entreprise->name }}"></div>
        <div>
          <div class="brand-name">{{ $entreprise->name }}</div>
          <div class="brand-sub">Système d'information</div>
        </div>
      </div>
    </div>
    <div class="header-doc">
      <div class="doc-type">Rapport de trésorerie</div>
      <div class="doc-number">{{ $period->label }}</div>
      <div class="doc-date">Généré le {{ now()->locale('fr')->translatedFormat('d F Y à H:i') }}</div>
    </div>
  </div>

  <div class="summary-band">
    <div class="summary-cell in">
      <h4>Total entrées</h4>
      <div class="val">{{ number_format($report['entrees'], 0, ',', ' ') }} FCFA</div>
    </div>
    <div class="summary-cell out">
      <h4>Total dépenses</h4>
      <div class="val">{{ number_format($report['depenses'], 0, ',', ' ') }} FCFA</div>
    </div>
    <div class="summary-cell">
      <h4>Solde de la période</h4>
      <div class="val">{{ number_format($report['solde'], 0, ',', ' ') }} FCFA</div>
    </div>
  </div>

  <div class="items-section">
    <table class="items-table">
      <thead>
        <tr>
          <th style="width:14%;">Date</th>
          <th style="width:12%;">Type</th>
          <th style="width:20%;">Catégorie</th>
          <th style="width:34%;">Description</th>
          <th class="amount" style="width:20%;">Montant</th>
        </tr>
      </thead>
      <tbody>
        @forelse($report['transactions'] as $t)
          <tr>
            <td>{{ $t->date->format('d/m/Y') }}</td>
            <td>
              <span class="type-pill {{ $t->type->value }}">{{ $t->type->value === 'in' ? 'Entrée' : 'Sortie' }}</span>
            </td>
            <td>{{ $t->categoryLabel() }}</td>
            <td>
              {{ $t->description ?? '—' }}
              @if($t->supplier_name || $t->product_reference)
                <div class="item-detail">
                  @if($t->supplier_name) {{ $t->supplier_name }} @endif
                  @if($t->supplier_name && $t->product_reference) — @endif
                  @if($t->product_reference) Réf. {{ $t->product_reference }} @endif
                </div>
              @endif
            </td>
            <td class="amount {{ $t->type->value }}">
              {{ $t->type->value === 'in' ? '+' : '-' }}{{ number_format($t->amount, 0, ',', ' ') }} FCFA
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="5" style="text-align:center;padding:24px;color:var(--text-muted);">Aucune opération sur cette période.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="footer-line">
    Tél : <strong>{{ $entreprise->phone }}</strong>
    &nbsp;&nbsp;·&nbsp;&nbsp;{{ $entreprise->address_line1 }}, {{ $entreprise->address_line2 }}
  </div>
</div>

</body>
</html>
