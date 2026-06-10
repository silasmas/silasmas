<?php

namespace App\Filament\Resources\SessionPaymentResource\Pages;

use App\Filament\Resources\SessionPaymentResource;
use App\Models\SessionPayment;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListSessionPayments extends ListRecords
{
  protected static string $resource = SessionPaymentResource::class;

  /**
   * Onglets : tous les paiements / échecs uniquement.
   *
   * @return array<string, Tab>
   */
  public function getTabs(): array
  {
    $failureCount = SessionPayment::failedOrCancelled()->count();

    return [
      'all' => Tab::make('Tous'),
      'failures' => Tab::make('Échecs & annulations')
        ->modifyQueryUsing(
          fn (Builder $query): Builder => $query->failedOrCancelled()->latest('failed_at')
        )
        ->badge($failureCount > 0 ? $failureCount : null)
        ->badgeColor('danger'),
    ];
  }

  protected function getHeaderActions(): array
  {
    return [
      Actions\CreateAction::make(),
    ];
  }
}
