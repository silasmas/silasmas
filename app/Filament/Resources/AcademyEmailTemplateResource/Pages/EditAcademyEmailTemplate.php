<?php

namespace App\Filament\Resources\AcademyEmailTemplateResource\Pages;

use App\Filament\Resources\AcademyEmailTemplateResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAcademyEmailTemplate extends EditRecord
{
  protected static string $resource = AcademyEmailTemplateResource::class;

  protected function getHeaderActions(): array
  {
    return [
      Actions\DeleteAction::make(),
    ];
  }
}
