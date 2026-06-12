<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\SiteVisitStatsWidget;
use App\Filament\Widgets\SiteVisitsByCountryChartWidget;
use App\Filament\Widgets\SiteVisitsByHourChartWidget;
use Filament\Pages\Page;

/**
 * Page dashboard — statistiques de fréquentation du site vitrine.
 */
class SiteAnalytics extends Page
{
  protected static ?string $navigationIcon = 'heroicon-o-chart-bar-square';

  protected static ?string $navigationGroup = 'Site vitrine';

  protected static ?int $navigationSort = 9;

  protected static ?string $navigationLabel = 'Statistiques visites';

  protected static ?string $title = 'Statistiques des visites';

  protected static string $view = 'filament.pages.site-analytics';

  /**
   * Widgets affichés sur la page analytics.
   *
   * @return array<class-string>
   */
  protected function getHeaderWidgets(): array
  {
    return [
      SiteVisitStatsWidget::class,
      SiteVisitsByHourChartWidget::class,
      SiteVisitsByCountryChartWidget::class,
    ];
  }
}
