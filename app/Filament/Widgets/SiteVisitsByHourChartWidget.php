<?php

namespace App\Filament\Widgets;

use App\Models\SiteVisit;
use Filament\Widgets\ChartWidget;

/**
 * Widget dashboard — répartition des visites par heure (7 derniers jours).
 */
class SiteVisitsByHourChartWidget extends ChartWidget
{
  protected static bool $isDiscovered = false;

  protected static ?string $heading = 'Visites par heure';

  protected static ?int $sort = 2;

  protected int|string|array $columnSpan = 'full';

  /**
   * Type de graphique Chart.js.
   */
  protected function getType(): string
  {
    return 'bar';
  }

  /**
   * Données agrégées par tranche horaire.
   *
   * @return array<string, mixed>
   */
  protected function getData(): array
  {
    $since = now()->subDays(7);
    $counts = array_fill(0, 24, 0);

    $rows = SiteVisit::pageViews()
      ->where('visited_at', '>=', $since)
      ->get(['visited_at']);

    foreach ($rows as $row) {
      $hour = (int) $row->visited_at->format('G');
      $counts[$hour]++;
    }

    $labels = [];

    for ($hour = 0; $hour < 24; $hour++) {
      $labels[] = sprintf('%02dh', $hour);
    }

    return [
      'datasets' => [
        [
          'label' => 'Pages vues',
          'data' => array_values($counts),
          'backgroundColor' => 'rgba(245, 158, 11, 0.65)',
          'borderColor' => 'rgba(217, 119, 6, 1)',
          'borderWidth' => 1,
        ],
      ],
      'labels' => $labels,
    ];
  }
}
