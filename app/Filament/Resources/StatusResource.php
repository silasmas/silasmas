<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StatusResource\Pages;
use App\Models\Status;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

/**
 * Ressource Filament pour les statuts (utilisateurs, projets, messages).
 */
class StatusResource extends Resource
{
  protected static ?string $model = Status::class;

  protected static ?string $navigationIcon = 'heroicon-o-signal';

  protected static ?string $navigationGroup = 'Configuration';

  protected static ?int $navigationSort = 10;

  protected static ?string $modelLabel = 'Statut';

  protected static ?string $pluralModelLabel = 'Statuts';

  /**
   * Formulaire de création / édition.
   */
  public static function form(Form $form): Form
  {
    return $form
      ->schema([
        Forms\Components\TextInput::make('status_name')
          ->label('Nom')
          ->required()
          ->maxLength(255)
          ->unique(ignoreRecord: true),
        Forms\Components\Textarea::make('status_description')
          ->label('Description')
          ->rows(3)
          ->columnSpanFull(),
        Forms\Components\ColorPicker::make('color')
          ->label('Couleur'),
      ]);
  }

  /**
   * Tableau de liste des statuts.
   */
  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        Tables\Columns\TextColumn::make('status_name')
          ->label('Nom')
          ->searchable()
          ->sortable(),
        Tables\Columns\ColorColumn::make('color')
          ->label('Couleur'),
        Tables\Columns\TextColumn::make('created_at')
          ->label('Créé le')
          ->dateTime('d/m/Y H:i')
          ->sortable()
          ->toggleable(isToggledHiddenByDefault: true),
      ])
      ->actions([
        Tables\Actions\EditAction::make(),
        Tables\Actions\DeleteAction::make(),
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
      'index' => Pages\ListStatuses::route('/'),
      'create' => Pages\CreateStatus::route('/create'),
      'edit' => Pages\EditStatus::route('/{record}/edit'),
    ];
  }
}
