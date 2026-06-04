<?php

namespace App\Filament\Resources\SiteSettingResource\Pages;

use App\Filament\Resources\SiteSettingResource;
use App\Models\SiteSetting;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ManageSiteSetting extends ListRecords
{
  protected static string $resource = SiteSettingResource::class;

  /**
   * Redirige vers l'unique fiche de paramétrage.
   */
  public function mount(): void
  {
    $settings = SiteSetting::instance();

    redirect(SiteSettingResource::getUrl('edit', ['record' => $settings]));
  }

  protected function getHeaderActions(): array
  {
    return [
      Actions\CreateAction::make()
        ->visible(fn (): bool => SiteSetting::query()->count() === 0),
    ];
  }
}
