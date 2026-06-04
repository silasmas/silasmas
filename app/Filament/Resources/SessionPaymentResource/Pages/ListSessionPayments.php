<?php

namespace App\Filament\Resources\SessionPaymentResource\Pages;

use App\Filament\Resources\SessionPaymentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSessionPayments extends ListRecords
{
  protected static string $resource = SessionPaymentResource::class;

  protected function getHeaderActions(): array
  {
    return [
      Actions\CreateAction::make(),
    ];
  }
}
