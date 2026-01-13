@php $year = date('Y'); @endphp

<footer id="contact" class="bg-card border-t">
  <div class="max-w-7xl mx-auto px-4 md:px-6 py-12">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
      <div class="md:col-span-2">
        <div class="flex items-center gap-2 mb-4">
          <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-primary text-primary-foreground">
            <i data-lucide="graduation-cap" class="h-6 w-6"></i>
          </div>
          <span class="font-semibold text-lg">Alumni Tracking System</span>
        </div>

        <p class="text-muted-foreground text-sm max-w-md">
          A comprehensive platform connecting graduates, institutions, and employers
          for professional growth and career development.
        </p>
      </div>

      <div>
        <h4 class="font-semibold mb-4">Quick Links</h4>
        <ul class="space-y-2 text-sm text-muted-foreground">
          <li><a href="/" class="hover:text-foreground transition-colors">{{ __('nav.home') }}</a></li>
          <li><a href="/#about" class="hover:text-foreground transition-colors">{{ __('nav.about') }}</a></li>
          <li><a href="/login" class="hover:text-foreground transition-colors">{{ __('nav.login') }}</a></li>
          <li><a href="#" class="hover:text-foreground transition-colors">{{ __('footer.privacy') }}</a></li>
          <li><a href="#" class="hover:text-foreground transition-colors">{{ __('footer.terms') }}</a></li>
        </ul>
      </div>

      <div>
        <h4 class="font-semibold mb-4">{{ __('footer.contact') }}</h4>
        <ul class="space-y-3 text-sm text-muted-foreground">
          <li class="flex items-center gap-2">
            <i data-lucide="mail" class="h-4 w-4"></i>
            alumni@college.edu
          </li>
          <li class="flex items-center gap-2">
            <i data-lucide="phone" class="h-4 w-4"></i>
            +970 8 123 4567
          </li>
          <li class="flex items-start gap-2">
            <i data-lucide="map-pin" class="h-4 w-4 mt-0.5"></i>
            <span>Palestine Technical College<br />Gaza, Palestine</span>
          </li>
        </ul>
      </div>
    </div>

    <div class="mt-12 pt-8 border-t flex flex-col sm:flex-row justify-between items-center gap-4">
      <p class="text-sm text-muted-foreground">
        &copy; {{ $year }} Alumni Tracking System. {{ __('footer.rights') }}.
      </p>
      <p class="text-sm text-muted-foreground">
        Palestine Technical College
      </p>
    </div>
  </div>
</footer>
