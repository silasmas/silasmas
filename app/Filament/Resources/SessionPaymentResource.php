<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SessionPaymentResource\Pages;
use App\Models\SessionPayment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

/**
 * Ressource Filament — paiements par session Academy.
 */
class SessionPaymentResource extends Resource
{
  protected static ?string $model = SessionPayment::class;

  protected static ?string $navigationIcon = 'heroicon-o-banknotes';

  protected static ?string $navigationGroup = 'SDev Academy';

  protected static ?int $navigationSort = 3;

  protected static ?string $modelLabel = 'Paiement';

  protected static ?string $pluralModelLabel = 'Paiements';

  protected static ?string $navigationLabel = 'Paiements';

  /**
   * Badge rouge si des paiements ont échoué.
   *
   * @return string|null Nombre d'échecs ou null
   */
  public static function getNavigationBadge(): ?string
  {
    $count = static::getModel()::failedOrCancelled()->count();

    return $count > 0 ? (string) $count : null;
  }

  /**
   * Couleur du badge navigation.
   *
   * @return string|null Couleur Filament
   */
  public static function getNavigationBadgeColor(): ?string
  {
    return 'danger';
  }

  /**
   * Formulaire de création / édition.
   */
  public static function form(Form $form): Form
  {
    return $form
      ->schema([
        Forms\Components\Select::make('training_session_id')
          ->label('Session')
          ->relationship('trainingSession', 'title')
          ->searchable()
          ->preload()
          ->required(),
        Forms\Components\Select::make('registration_id')
          ->label('Inscription')
          ->relationship('registration', 'id')
          ->getOptionLabelFromRecordUsing(
            fn ($record) => '#'.$record->id.' — '.($record->student?->email ?? 'N/A')
          )
          ->searchable()
          ->preload(),
        Forms\Components\Select::make('student_id')
          ->label('Étudiant')
          ->relationship('student', 'email')
          ->getOptionLabelFromRecordUsing(
            fn ($record) => trim($record->firstname.' '.$record->lastname).' ('.$record->email.')'
          )
          ->searchable(['firstname', 'lastname', 'email'])
          ->preload(),
        Forms\Components\TextInput::make('amount')
          ->label('Montant')
          ->numeric()
          ->required()
          ->minValue(0),
        Forms\Components\TextInput::make('currency')
          ->label('Devise')
          ->default('USD')
          ->maxLength(3)
          ->required(),
        Forms\Components\Select::make('status')
          ->label('Statut')
          ->options([
            'pending' => 'En attente',
            'processing' => 'En cours (FlexPay)',
            'paid' => 'Payé',
            'failed' => 'Échoué',
            'refunded' => 'Remboursé',
            'cancelled' => 'Annulé',
          ])
          ->required(),
        Forms\Components\Select::make('payment_method')
          ->label('Méthode')
          ->options([
            'mobile_money' => 'Mobile Money',
            'bank' => 'Virement bancaire',
            'cash' => 'Espèces',
            'card' => 'Carte',
            'other' => 'Autre',
          ]),
        Forms\Components\TextInput::make('reference')
          ->label('Référence')
          ->maxLength(255)
          ->unique(ignoreRecord: true),
        Forms\Components\DateTimePicker::make('paid_at')
          ->label('Date de paiement'),
        Forms\Components\Textarea::make('notes')
          ->label('Notes')
          ->rows(3)
          ->columnSpanFull(),
        Forms\Components\TextInput::make('failure_context')
          ->label('Contexte échec')
          ->disabled()
          ->dehydrated(false)
          ->formatStateUsing(fn (?string $state): string => SessionPayment::failureContextLabel($state)),
        Forms\Components\Textarea::make('failure_reason')
          ->label('Raison de l\'échec')
          ->rows(3)
          ->disabled()
          ->columnSpanFull(),
        Forms\Components\Textarea::make('failure_server_response')
          ->label('Réponse serveur (FlexPay / API)')
          ->rows(10)
          ->disabled()
          ->formatStateUsing(fn (?string $state, SessionPayment $record): string => $record->formattedServerResponse())
          ->columnSpanFull(),
        Forms\Components\DateTimePicker::make('failed_at')
          ->label('Échec le')
          ->disabled(),
        Forms\Components\DateTimePicker::make('admin_notified_at')
          ->label('Alerte admin envoyée le')
          ->disabled(),
      ])
      ->columns(2);
  }

  /**
   * Tableau de liste des paiements.
   */
  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        Tables\Columns\TextColumn::make('trainingSession.title')
          ->label('Session')
          ->searchable()
          ->limit(30),
        Tables\Columns\TextColumn::make('student.email')
          ->label('Étudiant')
          ->toggleable(),
        Tables\Columns\TextColumn::make('amount')
          ->label('Montant')
          ->money(fn ($record) => $record->currency ?? 'USD')
          ->sortable(),
        Tables\Columns\TextColumn::make('status')
          ->label('Statut')
          ->badge()
          ->color(fn (string $state): string => match ($state) {
            'paid' => 'success',
            'pending' => 'warning',
            'processing' => 'info',
            'failed' => 'danger',
            'cancelled' => 'danger',
            'refunded' => 'info',
            default => 'gray',
          })
          ->formatStateUsing(fn (string $state): string => match ($state) {
            'pending' => 'En attente',
            'processing' => 'En cours',
            'paid' => 'Payé',
            'failed' => 'Échoué',
            'cancelled' => 'Annulé',
            'refunded' => 'Remboursé',
            default => $state,
          }),
        Tables\Columns\TextColumn::make('failure_context')
          ->label('Contexte')
          ->toggleable(isToggledHiddenByDefault: true)
          ->formatStateUsing(fn (?string $state): string => SessionPayment::failureContextLabel($state)),
        Tables\Columns\TextColumn::make('failure_reason')
          ->label('Raison')
          ->limit(35)
          ->tooltip(fn (?string $state): ?string => $state)
          ->toggleable(),
        Tables\Columns\TextColumn::make('failure_server_response')
          ->label('Réponse serveur')
          ->limit(40)
          ->tooltip(fn (SessionPayment $record): string => $record->formattedServerResponse())
          ->toggleable(),
        Tables\Columns\TextColumn::make('failed_at')
          ->label('Échec le')
          ->dateTime('d/m/Y H:i')
          ->sortable()
          ->toggleable(),
        Tables\Columns\TextColumn::make('payment_method')
          ->label('Méthode')
          ->toggleable(),
        Tables\Columns\TextColumn::make('reference')
          ->label('Référence')
          ->searchable()
          ->toggleable(),
        Tables\Columns\TextColumn::make('paid_at')
          ->label('Payé le')
          ->dateTime('d/m/Y H:i')
          ->sortable(),
      ])
      ->defaultSort('created_at', 'desc')
      ->filters([
        Tables\Filters\SelectFilter::make('training_session_id')
          ->label('Session')
          ->relationship('trainingSession', 'title'),
        Tables\Filters\SelectFilter::make('status')
          ->label('Statut')
          ->options([
            'pending' => 'En attente',
            'processing' => 'En cours (FlexPay)',
            'paid' => 'Payé',
            'failed' => 'Échoué',
            'refunded' => 'Remboursé',
            'cancelled' => 'Annulé',
          ]),
      ])
      ->actions([
        Tables\Actions\EditAction::make(),
      ])
      ->bulkActions([
        Tables\Actions\BulkActionGroup::make([
          Tables\Actions\DeleteBulkAction::make(),
        ]),
      ]);
  }

  /**
   * @return array<class-string>
   */
  public static function getPages(): array
  {
    return [
      'index' => Pages\ListSessionPayments::route('/'),
      'create' => Pages\CreateSessionPayment::route('/create'),
      'edit' => Pages\EditSessionPayment::route('/{record}/edit'),
    ];
  }
}
