<div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-900">
  <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-gray-500">Objet</p>
  <p class="mb-4 text-sm font-medium text-gray-900 dark:text-gray-100">{{ $subject }}</p>

  @if(!empty($preview_hint) && empty($payment_resume_url))
    <p class="mb-3 rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-xs text-red-800 dark:border-red-800 dark:bg-red-950 dark:text-red-200">
      {{ $preview_hint }}
    </p>
  @endif

  @if(!empty($payment_resume_url) && str_contains($body_html ?? '', '#inscription'))
    <p class="mb-3 rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-xs text-amber-800 dark:border-amber-800 dark:bg-amber-950 dark:text-amber-200">
      Le corps contient encore un lien <code>#inscription</code> — à l'envoi, il sera remplacé par le lien de reprise paiement ci-dessous.
    </p>
  @endif

  <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-gray-500">Message</p>
  <div class="prose prose-sm max-w-none text-gray-800 dark:prose-invert dark:text-gray-200">
    @if(($firstname ?? '') !== '')
      <p>Bonjour <strong>{{ $firstname }}</strong>,</p>
    @else
      <p>Bonjour,</p>
    @endif

    {!! $body_html !!}

    @if(!empty($payment_resume_url))
      <p style="margin: 1.5rem 0;">
        <a href="{{ $payment_resume_url }}"
           style="display:inline-block;padding:12px 24px;background:#c87832;color:#fff;text-decoration:none;border-radius:8px;font-weight:600;">
          Finaliser mon paiement
        </a>
      </p>
      <p class="text-xs text-gray-500">Lien direct : {{ $payment_resume_url }}</p>
    @endif

    <p class="mt-4 text-sm text-gray-600">Merci,<br><strong>SDev Academy</strong></p>
  </div>
</div>
