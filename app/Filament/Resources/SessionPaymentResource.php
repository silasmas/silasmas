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
            'failed' => 'danger',
            'refunded' => 'info',
            default => 'gray',
          }),
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
