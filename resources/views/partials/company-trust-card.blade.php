@php
  $company = $company ?? null;
  $profile = $profile ?? $company?->companyProfile;
  $variant = $variant ?? 'card';
  $fallbackName = $fallbackName ?? null;

  $displayName = $profile?->company_name ?: ($fallbackName ?: ($company?->name ?: __('Company')));
  $industry = $profile?->industry;
  $location = $profile?->location;
  $description = $profile?->description;
  $website = $profile?->website;
  $contactPerson = $profile?->contact_person_name;
  $email = $company?->email;
  $status = strtolower((string) ($profile?->status ?? ''));
  $isApproved = $status === 'approved';
  $approvedAt = $profile?->approved_at;

  $websiteHref = null;
  if (!empty($website)) {
      $websiteHref = preg_match('/^https?:\/\//i', $website) ? $website : 'https://' . $website;
  }

  $initials = collect(explode(' ', (string) $displayName))
      ->filter()
      ->map(fn ($part) => mb_substr($part, 0, 1))
      ->take(2)
      ->join('') ?: 'CO';

  $shellClass = $variant === 'embedded'
      ? 'mt-4 border-t border-border pt-4'
      : 'rounded-xl border border-border bg-card p-4 sm:p-6';
@endphp

<section class="{{ $shellClass }}">
  <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
    <div class="flex min-w-0 items-start gap-3">
      <div class="flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-lg bg-primary/10 text-sm font-bold text-primary">
        {{ $initials }}
      </div>

      <div class="min-w-0">
        <div class="flex flex-wrap items-center gap-2">
          <h2 class="text-base font-semibold leading-snug break-words">{{ $displayName }}</h2>
          @if($isApproved)
            <span class="inline-flex items-center gap-1 rounded-full bg-green-500/15 px-2 py-0.5 text-xs font-medium text-green-400">
              <i data-lucide="badge-check" class="h-3 w-3"></i>
              Verified
            </span>
          @endif
        </div>

        <p class="mt-1 text-xs text-muted-foreground">
          Review this company before you apply or register.
        </p>
      </div>
    </div>

    @if($websiteHref)
      <a href="{{ $websiteHref }}" target="_blank" rel="noopener noreferrer"
         class="inline-flex w-full items-center justify-center gap-2 rounded-md border border-border px-3 py-2 text-sm hover:bg-accent/50 sm:w-auto">
        <i data-lucide="external-link" class="h-4 w-4"></i>
        Website
      </a>
    @endif
  </div>

  @if($description)
    <p class="mt-4 text-sm leading-relaxed text-muted-foreground whitespace-pre-line break-words">{{ $description }}</p>
  @else
    <p class="mt-4 text-sm leading-relaxed text-muted-foreground">
      This company has not added a full description yet. Use the available details below before making your decision.
    </p>
  @endif

  <div class="mt-4 grid gap-3 text-sm sm:grid-cols-2">
    @if($industry)
      <div class="min-w-0 rounded-md bg-muted/40 px-3 py-2">
        <div class="text-xs text-muted-foreground">Industry</div>
        <div class="font-medium break-words">{{ $industry }}</div>
      </div>
    @endif

    @if($location)
      <div class="min-w-0 rounded-md bg-muted/40 px-3 py-2">
        <div class="text-xs text-muted-foreground">Location</div>
        <div class="font-medium break-words">{{ $location }}</div>
      </div>
    @endif

    @if($email)
      <div class="min-w-0 rounded-md bg-muted/40 px-3 py-2">
        <div class="text-xs text-muted-foreground">Official Email</div>
        <div class="font-medium break-all">{{ $email }}</div>
      </div>
    @endif

    @if($contactPerson)
      <div class="min-w-0 rounded-md bg-muted/40 px-3 py-2">
        <div class="text-xs text-muted-foreground">Contact Person</div>
        <div class="font-medium break-words">{{ $contactPerson }}</div>
      </div>
    @endif

    @if($approvedAt)
      <div class="min-w-0 rounded-md bg-muted/40 px-3 py-2">
        <div class="text-xs text-muted-foreground">Approved Since</div>
        <div class="font-medium">{{ $approvedAt->format('M d, Y') }}</div>
      </div>
    @endif
  </div>
</section>
