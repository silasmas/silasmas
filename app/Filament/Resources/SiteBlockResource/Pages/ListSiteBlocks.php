<?php

namespace App\Filament\Resources\SiteBlockResource\Pages;

use App\Filament\Resources\SiteBlockResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSiteBlocks extends ListRecords
{
  protected static string $resource = SiteBlockResource::class;

  protected function getHeaderActions(): array
  {
    return [
      Actions\CreateAction::make(),
    ];
  }
}
