@php
  $items = [];

  if (session('toast_success')) $items[] = ['type'=>'success','title'=>'Success','message'=>session('toast_success')];
  if (session('toast_error'))   $items[] = ['type'=>'error','title'=>'Error','message'=>session('toast_error')];
  if (session('toast_info'))    $items[] = ['type'=>'info','title'=>'Info','message'=>session('toast_info')];
  if (session('toast_warning')) $items[] = ['type'=>'warning','title'=>'Warning','message'=>session('toast_warning')];
@endphp

@if(count($items))
  <div id="toastContainer"
       class="fixed bottom-6 right-6 z-[9999] w-[360px] max-w-[92vw] space-y-3 pointer-events-none">
    @foreach($items as $t)
      @php
        $type = $t['type'];

        $accent = match($type) {
          'success' => 'border-green-500/30 bg-green-500/10 text-green-200',
          'error'   => 'border-red-500/30 bg-red-500/10 text-red-200',
          'warning' => 'border-yellow-500/30 bg-yellow-500/10 text-yellow-200',
          default   => 'border-primary/30 bg-primary/10 text-foreground',
        };

        $icon = match($type) {
          'success' => 'check-circle-2',
          'error'   => 'x-circle',
          'warning' => 'alert-triangle',
          default   => 'info',
        };
      @endphp

      <div class="toastItem pointer-events-auto overflow-hidden rounded-2xl border border-border bg-card shadow-2xl
                  transition duration-300"
           data-timeout="2500">

        <div class="border-l-4 {{ $accent }} px-4 py-3">
          <div class="flex items-start gap-3">
            <div class="mt-0.5 h-9 w-9 rounded-xl bg-background/40 flex items-center justify-center">
              <i data-lucide="{{ $icon }}" class="h-5 w-5"></i>
            </div>

            <div class="min-w-0 flex-1">
              <div class="text-sm font-semibold">{{ $t['title'] }}</div>
              <div class="text-sm text-muted-foreground mt-0.5 break-words">
                {{ $t['message'] }}
              </div>
            </div>

            <button type="button"
                    class="h-8 w-8 inline-flex items-center justify-center rounded-md hover:bg-accent/50"
                    onclick="this.closest('.toastItem')?.remove()"
                    aria-label="Close">
              <i data-lucide="x" class="h-4 w-4"></i>
            </button>
          </div>
        </div>

      </div>
    @endforeach
  </div>

  <script>
    (function () {
      document.querySelectorAll('.toastItem').forEach((el) => {
        const ms = parseInt(el.getAttribute('data-timeout') || '3500', 10);
        setTimeout(() => {
          el.classList.add('opacity-0', 'translate-y-2');
          setTimeout(() => el.remove(), 300);
        }, ms);
      });
    })();
  </script>
@endif
