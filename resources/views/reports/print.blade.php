<!doctype html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ $report['title'] }} | PTC Alumni Tracking System</title>
  <style>
    :root {
      --accent: {{ $report['accent'] ?? '#2563eb' }};
      --ink: #111827;
      --muted: #6b7280;
      --line: #e5e7eb;
      --soft: #f8fafc;
    }

    * { box-sizing: border-box; }

    body {
      margin: 0;
      background: #eef2f7;
      color: var(--ink);
      font-family: Arial, "Segoe UI", Tahoma, sans-serif;
      line-height: 1.5;
    }

    .page {
      width: min(1120px, calc(100% - 32px));
      margin: 24px auto;
      background: #ffffff;
      border: 1px solid var(--line);
      border-radius: 8px;
      overflow: hidden;
      box-shadow: 0 18px 45px rgba(15, 23, 42, 0.10);
    }

    .report-header {
      padding: 30px 34px;
      color: #ffffff;
      background: linear-gradient(135deg, #111827 0%, #1f2937 62%, var(--accent) 100%);
    }

    .eyebrow {
      margin: 0 0 8px;
      font-size: 12px;
      letter-spacing: 0;
      text-transform: uppercase;
      opacity: 0.82;
    }

    h1 {
      margin: 0;
      font-size: 30px;
      line-height: 1.2;
    }

    .subtitle {
      margin: 10px 0 0;
      max-width: 760px;
      color: rgba(255, 255, 255, 0.84);
      font-size: 14px;
    }

    .meta {
      display: grid;
      grid-template-columns: repeat(4, minmax(0, 1fr));
      gap: 12px;
      padding: 18px 34px;
      background: var(--soft);
      border-bottom: 1px solid var(--line);
    }

    .meta-item span,
    .card span {
      display: block;
      color: var(--muted);
      font-size: 12px;
      margin-bottom: 4px;
    }

    .meta-item strong,
    .card strong {
      display: block;
      font-size: 15px;
    }

    .content {
      padding: 28px 34px 34px;
    }

    .insights {
      display: grid;
      grid-template-columns: repeat(4, minmax(0, 1fr));
      gap: 12px;
      margin-bottom: 20px;
    }

    .insight {
      border-left: 4px solid var(--accent);
      border-radius: 8px;
      padding: 14px;
      background: var(--soft);
    }

    .insight span {
      display: block;
      color: var(--muted);
      font-size: 12px;
      margin-bottom: 4px;
    }

    .insight strong {
      display: block;
      font-size: 16px;
    }

    .cards {
      display: grid;
      grid-template-columns: repeat(4, minmax(0, 1fr));
      gap: 14px;
      margin-bottom: 26px;
    }

    .card {
      border: 1px solid var(--line);
      border-radius: 8px;
      padding: 16px;
      background: #ffffff;
    }

    .card strong {
      color: var(--accent);
      font-size: 28px;
      line-height: 1.1;
      margin-bottom: 6px;
    }

    .section {
      margin-top: 24px;
      page-break-inside: avoid;
    }

    .section-title {
      display: flex;
      align-items: center;
      gap: 10px;
      margin: 0 0 12px;
      font-size: 18px;
    }

    .section-title::before {
      content: "";
      width: 5px;
      height: 22px;
      border-radius: 6px;
      background: var(--accent);
      flex: 0 0 auto;
    }

    .section-description {
      margin: -6px 0 12px;
      color: var(--muted);
      font-size: 12px;
    }

    .table-wrap {
      width: 100%;
      overflow-x: auto;
      border: 1px solid var(--line);
      border-radius: 8px;
    }

    table {
      width: 100%;
      min-width: 680px;
      border-collapse: collapse;
      overflow: hidden;
    }

    th {
      background: #f3f4f6;
      color: #374151;
      font-size: 12px;
      text-transform: uppercase;
      letter-spacing: 0;
    }

    th, td {
      padding: 11px 12px;
      border-bottom: 1px solid var(--line);
      border-right: 1px solid var(--line);
      text-align: start;
      vertical-align: middle;
      font-size: 13px;
      overflow-wrap: anywhere;
    }

    th:last-child,
    td:last-child { border-right: 0; }
    tr:last-child td { border-bottom: 0; }
    tbody tr:nth-child(even) td { background: #fbfdff; }

    .metric-cell {
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .bar {
      width: 120px;
      height: 8px;
      border-radius: 8px;
      background: #e5e7eb;
      overflow: hidden;
      flex: 0 0 auto;
    }

    .bar span {
      display: block;
      height: 100%;
      border-radius: 8px;
      background: var(--accent);
    }

    .actions {
      position: sticky;
      top: 0;
      z-index: 10;
      display: flex;
      justify-content: center;
      gap: 10px;
      padding: 12px;
      background: rgba(238, 242, 247, 0.92);
      backdrop-filter: blur(8px);
    }

    .actions button {
      border: 0;
      border-radius: 8px;
      padding: 10px 16px;
      color: #ffffff;
      background: var(--accent);
      cursor: pointer;
      font-weight: 700;
    }

    .actions a {
      border: 1px solid var(--line);
      border-radius: 8px;
      padding: 10px 16px;
      color: var(--ink);
      background: #ffffff;
      text-decoration: none;
      font-weight: 700;
    }

    @media (max-width: 820px) {
      .cards, .meta, .insights { grid-template-columns: repeat(2, minmax(0, 1fr)); }
      .page { width: calc(100% - 18px); margin: 12px auto; }
      .report-header, .meta, .content { padding-left: 18px; padding-right: 18px; }
    }

    @page { size: A4; margin: 12mm; }

    @media print {
      body { background: #ffffff; }
      .actions { display: none; }
      .page {
        width: 100%;
        margin: 0;
        border: 0;
        border-radius: 0;
        box-shadow: none;
      }
      .report-header,
      .card,
      .insight,
      th,
      tbody tr:nth-child(even) td,
      .bar span {
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
      }
      .section { break-inside: avoid; }
      table { min-width: 0; }
      th, td { font-size: 10px; padding: 7px; }
      .bar { width: 70px; }
    }
  </style>
</head>
<body>
  <div class="actions">
    <button type="button" onclick="window.print()">Save as PDF</button>
    <a href="javascript:history.back()">Back</a>
  </div>

  <article class="page">
    <header class="report-header">
      <p class="eyebrow">PTC Alumni Tracking System</p>
      <h1>{{ $report['title'] }}</h1>
      <p class="subtitle">{{ $report['subtitle'] }}</p>
    </header>

    <section class="meta">
      <div class="meta-item">
        <span>Generated At</span>
        <strong>{{ $report['generated_at'] }}</strong>
      </div>
      <div class="meta-item">
        <span>Generated By</span>
        <strong>{{ $report['generated_by'] }}</strong>
      </div>
      <div class="meta-item">
        <span>Reporting Period</span>
        <strong>{{ $report['period'] ?? 'Current snapshot' }}</strong>
      </div>
      <div class="meta-item">
        <span>Report Type</span>
        <strong>Detailed Executive Report</strong>
      </div>
    </section>

    <main class="content">
      @if(!empty($report['insights']))
        <section class="insights">
          @foreach($report['insights'] as $insight)
            <div class="insight">
              <span>{{ $insight['label'] }}</span>
              <strong>{{ $insight['value'] }}</strong>
            </div>
          @endforeach
        </section>
      @endif

      <section class="cards">
        @foreach($report['cards'] as $card)
          <div class="card">
            <span>{{ $card['label'] }}</span>
            <strong>{{ $card['value'] }}</strong>
            <span>{{ $card['note'] }}</span>
          </div>
        @endforeach
      </section>

      @foreach($report['sections'] as $section)
        <section class="section">
          <h2 class="section-title">{{ $section['title'] }}</h2>
          @if(!empty($section['description']))
            <p class="section-description">{{ $section['description'] }}</p>
          @endif

          <div class="table-wrap">
            <table>
              <thead>
                <tr>
                  @foreach($section['columns'] as $column)
                    <th>{{ $column }}</th>
                  @endforeach
                </tr>
              </thead>
              <tbody>
                @foreach($section['rows'] as $row)
                  <tr>
                    @foreach($row as $cell)
                      @php
                        $percent = is_string($cell) && preg_match('/^\d+%$/', $cell) ? (int) $cell : null;
                      @endphp
                      <td>
                        @if($percent !== null)
                          <div class="metric-cell">
                            <div class="bar"><span style="width: {{ min($percent, 100) }}%"></span></div>
                            <strong>{{ $cell }}</strong>
                          </div>
                        @else
                          {{ $cell }}
                        @endif
                      </td>
                    @endforeach
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </section>
      @endforeach
    </main>
  </article>

  <script>
    window.addEventListener('load', function () {
      setTimeout(function () {
        window.print();
      }, 350);
    });
  </script>
</body>
</html>
