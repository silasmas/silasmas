<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\SessionPaymentResource;
use App\Models\SessionPayment;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

/**
 * Widget dashboard — derniers paiements Academy échoués ou annulés.
 */
class FailedAcademyPaymentsWidget extends BaseWidget
{
  protected static ?int $sort = 2;

  protected int|string|array $columnSpan = 'full';

  /**
   * Affiche le widget uniquement s'il existe des échecs.
   *
   * @return bool true si au moins un échec
   */
  public static function canView(): bool
  {
    return SessionPayment::failedOrCancelled()->exists();
  }

  /**
   * Table des paiements en échec.
   *
   * @param Table $table Configuration Filament
   * @return Table
   */
  public function table(Table $table): Table
  {
    return $table
      ->query(
        SessionPayment::failedOrCancelled()
          ->with(['trainingSession', 'student', 'registration.student'])
          ->latest('failed_at')
          ->limit(10)
      )
      ->heading('Paiements échoués ou annulés')
      ->description('Surveillance automatique des inscriptions Academy')
      ->columns([
        Tables\Columns\TextColumn::make('reference')
          ->label('Référence')
          ->searchable()
          ->copyable(),
        Tables\Columns\TextColumn::make('trainingSession.title')
          ->label('Session')
          ->limit(28),
        Tables\Columns\TextColumn::make('student.email')
          ->label('Étudiant')
          ->default(fn (SessionPayment $record) => $record->registration?->student?->email ?? '—'),
        Tables\Columns\TextColumn::make('amount')
          ->label('Montant')
          ->money(fn (SessionPayment $record) => $record->currency ?? 'USD'),
        Tables\Columns\TextColumn::make('status')
          ->label('Statut')
          ->badge()
          ->color(fn (string $state): string => match ($state) {
            'failed' => 'danger',
            'cancelled' => 'warning',
            default => 'gray',
          })
          ->formatStateUsing(fn (string $state): string => match ($state) {
            'failed' => 'Échoué',
            'cancelled' => 'Annulé',
            default => $state,
          }),
        Tables\Columns\TextColumn::make('failure_context')
          ->label('Contexte')
          ->formatStateUsing(fn (?string $state): string => SessionPayment::failureContextLabel($state)),
        Tables\Columns\TextColumn::make('failure_reason')
          ->label('Motif')
          ->limit(40)
          ->tooltip(fn (?string $state): ?string => $state),
        Tables\Columns\TextColumn::make('failed_at')
          ->label('Échec le')
          ->dateTime('d/m/Y H:i')
          ->sortable(),
      ])
      ->actions([
        Tables\Actions\Action::make('open')
          ->label('Ouvrir')
          ->icon('heroicon-o-arrow-top-right-on-square')
          ->url(fn (SessionPayment $record): string => SessionPaymentResource::getUrl('edit', ['record' => $record])),
      ])
      ->paginated(false);
  }
}
