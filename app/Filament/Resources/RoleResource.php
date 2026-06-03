<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoleResource\Pages;
use App\Models\Role;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

/**
 * Ressource Filament pour les rôles utilisateurs.
 */
class RoleResource extends Resource
{
  protected static ?string $model = Role::class;

  protected static ?string $navigationIcon = 'heroicon-o-shield-check';

  protected static ?string $navigationGroup = 'Configuration';

  protected static ?int $navigationSort = 11;

  protected static ?string $modelLabel = 'Rôle';

  protected static ?string $pluralModelLabel = 'Rôles';

  /**
   * Formulaire de création / édition.
   */
  public static function form(Form $form): Form
  {
    return $form
      ->schema([
        Forms\Components\TextInput::make('role_name')
          ->label('Nom')
          ->required()
          ->maxLength(255)
          ->unique(ignoreRecord: true),
        Forms\Components\Textarea::make('role_description')
          ->label('Description')
          ->rows(3)
          ->columnSpanFull(),
      ]);
  }

  /**
   * Tableau de liste des rôles.
   */
  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        Tables\Columns\TextColumn::make('role_name')
          ->label('Nom')
          ->searchable()
          ->sortable(),
        Tables\Columns\TextColumn::make('users_count')
          ->label('Utilisateurs')
          ->counts('users'),
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
      'index' => Pages\ListRoles::route('/'),
      'create' => Pages\CreateRole::route('/create'),
      'edit' => Pages\EditRole::route('/{record}/edit'),
    ];
  }
}
