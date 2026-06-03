<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WebsiteResource\Pages;
use App\Models\Website;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

/**
 * Ressource Filament pour les sites web référencés.
 */
class WebsiteResource extends Resource
{
  protected static ?string $model = Website::class;

  protected static ?string $navigationIcon = 'heroicon-o-globe-alt';

  protected static ?string $navigationGroup = 'Contenu';

  protected static ?int $navigationSort = 3;

  protected static ?string $modelLabel = 'Site web';

  protected static ?string $pluralModelLabel = 'Sites web';

  /**
   * Formulaire de création / édition.
   */
  public static function form(Form $form): Form
  {
    return $form
      ->schema([
        Forms\Components\TextInput::make('website_name')
          ->label('Nom')
          ->maxLength(255),
        Forms\Components\TextInput::make('website_url')
          ->label('URL')
          ->url()
          ->required()
          ->maxLength(255)
          ->unique(ignoreRecord: true),
        Forms\Components\FileUpload::make('logo_url')
          ->label('Logo')
          ->image()
          ->disk('public')
          ->directory('images/websites')
          ->visibility('public'),
        Forms\Components\TextInput::make('icon')
          ->label('Icône (classe CSS ou URL)')
          ->maxLength(255),
        Forms\Components\Select::make('user_id')
          ->label('Propriétaire')
          ->relationship('user', 'email')
          ->getOptionLabelFromRecordUsing(fn ($record) => $record->name)
          ->searchable(['firstname', 'lastname', 'email'])
          ->preload(),
      ]);
  }

  /**
   * Tableau de liste des sites.
   */
  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        Tables\Columns\ImageColumn::make('logo_url')
          ->label('Logo')
          ->disk('public'),
        Tables\Columns\TextColumn::make('website_name')
          ->label('Nom')
          ->searchable(),
        Tables\Columns\TextColumn::make('website_url')
          ->label('URL')
          ->url(fn ($record) => $record->website_url)
          ->openUrlInNewTab()
          ->limit(40),
        Tables\Columns\TextColumn::make('user.name')
          ->label('Propriétaire')
          ->toggleable(),
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
      'index' => Pages\ListWebsites::route('/'),
      'create' => Pages\CreateWebsite::route('/create'),
      'edit' => Pages\EditWebsite::route('/{record}/edit'),
    ];
  }
}
