<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Admin Report</title>
  <style>
    body { font-family: Arial, sans-serif; padding: 24px; }
    table { width: 100%; border-collapse: collapse; margin-top: 16px; }
    th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
    th { background: #f5f5f5; }
    @media print { button { display:none; } }
  </style>
</head>
<body>
  <h2>Admin Report</h2>
  <p>Generated at: {{ now()->format('Y-m-d H:i') }}</p>

  <button onclick="window.print()">Print / Save as PDF</button>

  <table>
    <thead>
      <tr><th>Metric</th><th>Value</th></tr>
    </thead>
    <tbody>
      @foreach($rows as $r)
        <tr>
          <td>{{ $r[0] }}</td>
          <td>{{ $r[1] }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>
</body>
</html>
