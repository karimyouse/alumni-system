@php
  $manifestPath = public_path('build/manifest.json');
  $manifest = file_exists($manifestPath)
      ? json_decode((string) file_get_contents($manifestPath), true)
      : [];

  $cssAsset = $manifest['resources/css/app.css']['file'] ?? null;
  $jsAsset = $manifest['resources/js/app.js']['file'] ?? null;
  $rtlVersion = file_exists(public_path('rtl-arabic.css'))
      ? filemtime(public_path('rtl-arabic.css'))
      : time();
@endphp

@if (is_string($cssAsset) && $cssAsset !== '')
  <link rel="stylesheet" href="{{ asset('build/' . ltrim($cssAsset, '/')) }}">
@endif

<link rel="stylesheet" href="{{ asset('rtl-arabic.css') }}?v={{ $rtlVersion }}">

@if (is_string($jsAsset) && $jsAsset !== '')
  <script type="module" src="{{ asset('build/' . ltrim($jsAsset, '/')) }}"></script>
@endif
