<?php

namespace App\Filament\Widgets;

use App\Models\SiteVisit;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

/**
 * Widget dashboard — synthèse des visites et clics sur le site vitrine.
 */
class SiteVisitStatsWidget extends BaseWidget
{
  protected static bool $isDiscovered = false;

  protected static ?int $sort = 1;

  protected int|string|array $columnSpan = 'full';

  /**
   * Cartes statistiques principales.
   *
   * @return array<Stat>
   */
  protected function getStats(): array
  {
    $sinceMonth = now()->subDays(30);
    $sinceToday = now()->startOfDay();

    $pageViewsMonth = SiteVisit::pageViews()->where('visited_at', '>=', $sinceMonth)->count();
    $clicksMonth = SiteVisit::clicks()->where('visited_at', '>=', $sinceMonth)->count();
    $pageViewsToday = SiteVisit::pageViews()->where('visited_at', '>=', $sinceToday)->count();
    $countries = SiteVisit::query()
      ->where('visited_at', '>=', $sinceMonth)
      ->distinct('country_code')
      ->count('country_code');

    return [
      Stat::make('Visites (30 j)', number_format($pageViewsMonth, 0, ',', ' '))
        ->description('Pages vues sur silasmas.com')
        ->descriptionIcon('heroicon-m-eye')
        ->color('success'),
      Stat::make('Clics (30 j)', number_format($clicksMonth, 0, ',', ' '))
        ->description('Boutons et liens suivis')
        ->descriptionIcon('heroicon-m-cursor-arrow-rays')
        ->color('warning'),
      Stat::make('Visites aujourd\'hui', number_format($pageViewsToday, 0, ',', ' '))
        ->description('Depuis minuit')
        ->descriptionIcon('heroicon-m-calendar-days')
        ->color('info'),
      Stat::make('Pays (30 j)', (string) $countries)
        ->description('Origines géographiques distinctes')
        ->descriptionIcon('heroicon-m-globe-alt')
        ->color('gray'),
    ];
  }
}
