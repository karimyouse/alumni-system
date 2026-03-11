@php
  $translationPayload = [];
  $arJsonPath = resource_path('lang/ar.json');

  if (file_exists($arJsonPath)) {
      $decoded = json_decode((string) file_get_contents($arJsonPath), true);
      if (is_array($decoded)) {
          foreach ($decoded as $key => $value) {
              if (!is_string($key) || !is_string($value)) {
                  continue;
              }

              if (trim($key) === '' || trim($value) === '') {
                  continue;
              }

              $translationPayload[$key] = $value;
          }
      }
  }
@endphp
<script>
  window.__APP_LOCALE__ = @json(app()->getLocale());
  window.__AR_TRANSLATIONS__ = @json($translationPayload, JSON_UNESCAPED_UNICODE);
</script>
