<?php

namespace App\Filament\Resources\RegistrationResource\Pages;

use App\Exports\RegistrationsExport;
use App\Filament\Resources\RegistrationResource;
use App\Models\Registration;
use App\Models\TrainingSession;
use App\Services\RegistrationPdfExporter;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Maatwebsite\Excel\Facades\Excel;

class ListRegistrations extends ListRecords
{
  protected static string $resource = RegistrationResource::class;

  protected function getHeaderActions(): array
  {
    return [
      Actions\Action::make('emailUnpaid')
        ->label('E-mail aux non payés')
        ->icon('heroicon-o-envelope')
        ->color('warning')
        ->form([
          Forms\Components\Select::make('training_session_id')
            ->label('Session')
            ->options(fn (): array => TrainingSession::query()->orderBy('title')->pluck('title', 'id')->all())
            ->searchable()
            ->helperText('Laissez vide pour toutes les sessions.'),
          ...RegistrationResource::emailTemplateFormSchema(),
        ])
        ->action(function (array $data): void {
          $query = Registration::query()->paymentIncomplete()->where('notify_email', true);

          if (! empty($data['training_session_id'])) {
            $query->where('training_session_id', $data['training_session_id']);
          }

          $records = $query->with(['student', 'trainingSession', 'latestPayment'])->get();

          if ($records->isEmpty()) {
            Notification::make()
              ->title('Aucun destinataire')
              ->body('Aucune inscription en attente de paiement avec notifications e-mail activées.')
              ->warning()
              ->send();

            return;
          }

          RegistrationResource::sendTemplatedEmails($records, (int) $data['template_id']);
        }),
      Actions\Action::make('exportExcel')
        ->label('Exporter Excel')
        ->icon('heroicon-o-table-cells')
        ->action(function () {
          $filename = 'inscriptions-'.now()->format('Y-m-d-His').'.xlsx';

          return Excel::download(
            new RegistrationsExport($this->getFilteredTableQuery()),
            $filename
          );
        }),
      Actions\Action::make('exportPdf')
        ->label('Exporter PDF')
        ->icon('heroicon-o-document-arrow-down')
        ->action(function () {
          $pdf = app(RegistrationPdfExporter::class)->export(
            $this->getFilteredTableQuery(),
            'Inscriptions Academy'
          );
          $filename = 'inscriptions-'.now()->format('Y-m-d-His').'.pdf';

          return response()->streamDownload(
            fn () => print ($pdf->output()),
            $filename,
            ['Content-Type' => 'application/pdf']
          );
        }),
      Actions\CreateAction::make(),
    ];
  }
}
