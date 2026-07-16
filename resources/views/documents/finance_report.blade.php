<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>{{ $report['title'] }}</title>
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Segoe UI', Arial, sans-serif; font-size: 11px; color: #1a1a2e; }
    .header { display: table; width: 100%; padding: 20px 24px; border-bottom: 2px solid {{ $entreprise->accent_color ?: '#153BFF' }}; }
    .header .brand { display: table-cell; vertical-align: middle; }
    .header .brand-name { font-size: 16px; font-weight: 700; }
    .header .meta { display: table-cell; text-align: right; vertical-align: middle; }
    .header .meta .title { font-size: 14px; font-weight: 700; color: {{ $entreprise->accent_color ?: '#153BFF' }}; text-transform: uppercase; }
    .header .meta .period { font-size: 10px; color: #5b6479; margin-top: 3px; }
    table.data { width: 100%; border-collapse: collapse; margin: 20px 24px; width: calc(100% - 48px); }
    table.data thead th {
      background: {{ $entreprise->accent_color ?: '#153BFF' }}; color: #fff;
      padding: 8px 10px; font-size: 10px; text-transform: uppercase; text-align: left;
    }
    table.data tbody td { padding: 7px 10px; border-bottom: 1px solid #e3e5ec; }
    table.data tbody tr:nth-child(even) { background: #f7f8fa; }
    .footer { margin: 20px 24px; font-size: 9px; color: #5b6479; text-align: center; }
  </style>
</head>
<body>
  <div class="header">
    <div class="brand">
      <div class="brand-name">{{ $entreprise->name }}</div>
    </div>
    <div class="meta">
      <div class="title">{{ $report['title'] }}</div>
      <div class="period">{{ $period->label }}</div>
    </div>
  </div>

  <table class="data">
    <thead>
      <tr>
        @foreach($report['columns'] as $label)
          <th>{{ $label }}</th>
        @endforeach
      </tr>
    </thead>
    <tbody>
      @forelse($report['rows'] as $row)
        <tr>
          @foreach($row as $value)
            <td>{{ $value }}</td>
          @endforeach
        </tr>
      @empty
        <tr><td colspan="{{ count($report['columns']) }}" style="text-align:center;padding:20px;color:#9aa5b8;">Aucune donnée pour cette période.</td></tr>
      @endforelse
    </tbody>
  </table>

  <div class="footer">Généré le {{ now()->locale('fr')->translatedFormat('d F Y à H:i') }} — {{ $entreprise->name }}</div>
</body>
</html>
