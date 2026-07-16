<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Journal de caisse — {{ $account->name }}</title>
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Segoe UI', Arial, sans-serif; font-size: 11px; color: #1a1a2e; }
    .header { display: table; width: 100%; padding: 20px 24px; border-bottom: 2px solid {{ $entreprise->accent_color ?: '#153BFF' }}; }
    .header .brand { display: table-cell; vertical-align: middle; }
    .header .brand-name { font-size: 16px; font-weight: 700; }
    .header .meta { display: table-cell; text-align: right; vertical-align: middle; }
    .header .meta .title { font-size: 14px; font-weight: 700; color: {{ $entreprise->accent_color ?: '#153BFF' }}; text-transform: uppercase; }
    .summary { display: table; width: calc(100% - 48px); margin: 20px 24px; border-collapse: collapse; }
    .summary .cell { display: table-cell; width: 25%; padding: 10px; border: 1px solid #e3e5ec; text-align: center; }
    .summary .cell .label { font-size: 9px; text-transform: uppercase; color: #5b6479; }
    .summary .cell .value { font-size: 14px; font-weight: 700; margin-top: 4px; }
    table.data { width: calc(100% - 48px); border-collapse: collapse; margin: 0 24px 20px; }
    table.data thead th {
      background: {{ $entreprise->accent_color ?: '#153BFF' }}; color: #fff;
      padding: 8px 10px; font-size: 10px; text-transform: uppercase; text-align: left;
    }
    table.data tbody td { padding: 7px 10px; border-bottom: 1px solid #e3e5ec; }
  </style>
</head>
<body>
  <div class="header">
    <div class="brand"><div class="brand-name">{{ $entreprise->name }}</div></div>
    <div class="meta">
      <div class="title">Journal de caisse</div>
      <div>{{ $account->name }} — {{ $date->locale('fr')->translatedFormat('d F Y') }}</div>
    </div>
  </div>

  <div class="summary">
    <div class="cell"><div class="label">Solde ouverture</div><div class="value">{{ number_format($data['opening'], 0, ',', ' ') }} FCFA</div></div>
    <div class="cell"><div class="label">Entrées</div><div class="value">{{ number_format($data['entries'], 0, ',', ' ') }} FCFA</div></div>
    <div class="cell"><div class="label">Sorties</div><div class="value">{{ number_format($data['exits'], 0, ',', ' ') }} FCFA</div></div>
    <div class="cell"><div class="label">Solde clôture</div><div class="value">{{ number_format($data['closing'], 0, ',', ' ') }} FCFA</div></div>
  </div>

  <table class="data">
    <thead>
      <tr><th>Heure</th><th>Type</th><th>Catégorie</th><th>Client/Fournisseur</th><th>Montant</th></tr>
    </thead>
    <tbody>
      @forelse($data['transactions'] as $t)
        <tr>
          <td>{{ $t->created_at->format('H:i') }}</td>
          <td>{{ $t->type->label() }}</td>
          <td>{{ $t->category->label() }}</td>
          <td>{{ $t->customer?->full_name ?? $t->supplier?->name ?? '—' }}</td>
          <td>{{ number_format((float) $t->amount, 0, ',', ' ') }} FCFA</td>
        </tr>
      @empty
        <tr><td colspan="5" style="text-align:center;padding:20px;color:#9aa5b8;">Aucun mouvement ce jour-là.</td></tr>
      @endforelse
    </tbody>
  </table>
</body>
</html>
