<?php

namespace App\Filament\Widgets;

use App\Models\SiteVisit;
use Filament\Widgets\ChartWidget;

/**
 * Widget dashboard — répartition des visites par pays (30 jours).
 */
class SiteVisitsByCountryChartWidget extends ChartWidget
{
  protected static bool $isDiscovered = false;

  protected static ?string $heading = 'Visites par pays (30 jours)';

  protected static ?int $sort = 3;

  protected int|string|array $columnSpan = 'full';

  /**
   * Type de graphique Chart.js.
   */
  protected function getType(): string
  {
    return 'doughnut';
  }

  /**
   * Données agrégées par pays.
   *
   * @return array<string, mixed>
   */
  protected function getData(): array
  {
    $since = now()->subDays(30);

    $rows = SiteVisit::pageViews()
      ->selectRaw('country_name, country_code, COUNT(*) as total')
      ->where('visited_at', '>=', $since)
      ->groupBy('country_code', 'country_name')
      ->orderByDesc('total')
      ->limit(10)
      ->get();

    $labels = $rows->map(fn ($row) => $row->country_name ?: $row->country_code)->all();
    $data = $rows->pluck('total')->all();

    return [
      'datasets' => [
        [
          'label' => 'Pages vues',
          'data' => $data,
          'backgroundColor' => [
            'rgba(245, 158, 11, 0.8)',
            'rgba(59, 130, 246, 0.8)',
            'rgba(16, 185, 129, 0.8)',
            'rgba(239, 68, 68, 0.8)',
            'rgba(139, 92, 246, 0.8)',
            'rgba(236, 72, 153, 0.8)',
            'rgba(14, 165, 233, 0.8)',
            'rgba(132, 204, 22, 0.8)',
            'rgba(249, 115, 22, 0.8)',
            'rgba(100, 116, 139, 0.8)',
          ],
        ],
      ],
      'labels' => $labels,
    ];
  }
}
