@php
    $currentLocale = app()->getLocale();
    $isArabic = $currentLocale === 'ar';
@endphp

<div class="relative" x-data="{ langOpen: false }" @click.outside="langOpen = false">
    <button @click="langOpen = !langOpen" type="button"
        class="h-9 w-9 inline-flex items-center justify-center rounded-md hover:bg-accent/50 relative"
        aria-label="Language">
        <i data-lucide="globe" class="h-4 w-4"></i>
        <span class="absolute -bottom-0.5 -right-0.5 text-[9px] font-bold leading-none">
            {{ $isArabic ? 'ع' : 'EN' }}
        </span>
    </button>

    <div x-show="langOpen"
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="absolute top-full mt-2 w-44 rounded-xl shadow-2xl border border-border bg-popover z-[9999] overflow-hidden {{ $isArabic ? 'left-0' : 'right-0' }}"
         style="display: none;">
        <div class="py-1">

            <a href="{{ route('language.switch', 'en') }}"
               class="flex items-center gap-3 px-4 py-2.5 text-sm transition-colors
                      {{ !$isArabic ? 'bg-accent text-accent-foreground font-semibold' : 'hover:bg-accent/50' }}">
                <span class="text-base">🇺🇸</span>
                <span>English</span>
                @if(!$isArabic)
                    <i data-lucide="check" class="h-3.5 w-3.5 ml-auto text-primary"></i>
                @endif
            </a>

            <div class="h-px bg-border mx-3"></div>

            <a href="{{ route('language.switch', 'ar') }}"
               class="flex items-center gap-3 px-4 py-2.5 text-sm transition-colors
                      {{ $isArabic ? 'bg-accent text-accent-foreground font-semibold' : 'hover:bg-accent/50' }}"
               dir="rtl">
                <span class="text-base">🇸🇦</span>
                <span>العربية</span>
                @if($isArabic)
                    <i data-lucide="check" class="h-3.5 w-3.5 mr-auto text-primary"></i>
                @endif
            </a>

        </div>
    </div>
</div>
