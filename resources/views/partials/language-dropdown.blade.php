@php
  $buttonClass = $buttonClass ?? 'h-9 w-9 inline-flex items-center justify-center rounded-md hover:bg-accent/50 transition';
  $buttonLabel = $buttonLabel ?? __('common.language');
  $menuWidth = $menuWidth ?? 'w-40';
  $redirectTo = request()->fullUrl();
  $isRtl = app()->getLocale() === 'ar';
  $menuAlignClass = $menuAlignClass ?? ($isRtl ? 'left-0 origin-top-left' : 'right-0 origin-top-right');
  $menuTextAlignClass = $menuTextAlignClass ?? ($isRtl ? 'text-right' : 'text-left');
@endphp

<div class="relative" data-lang-dropdown>
  <button type="button"
          class="{{ $buttonClass }}"
          data-lang-toggle
          aria-haspopup="true"
          aria-expanded="false"
          aria-label="{{ $buttonLabel }}">
    <i data-lucide="globe" class="h-4 w-4"></i>
  </button>

  <div class="hidden absolute {{ $menuAlignClass }} mt-2 {{ $menuWidth }} rounded-xl border border-border bg-popover text-popover-foreground shadow-2xl z-[9999] overflow-hidden"
       data-lang-menu>
    @foreach(['en' => __('lang.english'), 'ar' => __('lang.arabic')] as $localeCode => $localeLabel)
      <form method="POST" action="{{ route('lang.switch') }}">
        @csrf
        <input type="hidden" name="locale" value="{{ $localeCode }}">
        <input type="hidden" name="redirect_to" value="{{ $redirectTo }}" data-lang-redirect>
        <input type="hidden" name="fragment" value="" data-lang-fragment>

        <button type="submit"
                class="w-full px-3 py-2.5 text-sm transition hover:bg-accent {{ app()->getLocale() === $localeCode ? 'bg-accent font-medium' : '' }} {{ $menuTextAlignClass }}">
          {{ $localeLabel }}
        </button>
      </form>
    @endforeach
  </div>
</div>
