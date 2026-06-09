<x-filament-panels::page>
  <div class="space-y-6">
    <x-filament::section>
      <x-slot name="heading">
        État actuel des migrations
      </x-slot>
      <x-slot name="description">
        Équivalent à <code class="text-xs">php artisan migrate:status</code>
      </x-slot>

      <pre
        class="max-h-96 overflow-auto rounded-lg border border-gray-200 bg-gray-50 p-4 text-xs leading-relaxed text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200"
      >{{ $statusOutput }}</pre>
    </x-filament::section>

    @if ($lastOutput !== null)
      <x-filament::section>
        <x-slot name="heading">
          Résultat de la dernière exécution
        </x-slot>
        <x-slot name="description">
          Équivalent à <code class="text-xs">php artisan migrate --force</code>
        </x-slot>

        <div
          class="mb-3 inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $lastSuccess ? 'bg-success-50 text-success-700 dark:bg-success-500/10 dark:text-success-400' : 'bg-danger-50 text-danger-700 dark:bg-danger-500/10 dark:text-danger-400' }}"
        >
          {{ $lastSuccess ? 'Succès' : 'Échec' }}
        </div>

        <pre
          class="max-h-96 overflow-auto rounded-lg border border-gray-200 bg-gray-50 p-4 text-xs leading-relaxed text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200"
        >{{ $lastOutput }}</pre>
      </x-filament::section>
    @endif
  </div>
</x-filament-panels::page>
