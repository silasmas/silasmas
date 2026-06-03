<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjectResource\Pages;
use App\Models\Project;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

/**
 * Ressource Filament pour le portfolio / projets SDEV.
 */
class ProjectResource extends Resource
{
  protected static ?string $model = Project::class;

  protected static ?string $navigationIcon = 'heroicon-o-briefcase';

  protected static ?string $navigationGroup = 'Contenu';

  protected static ?int $navigationSort = 1;

  protected static ?string $modelLabel = 'Projet';

  protected static ?string $pluralModelLabel = 'Projets';

  /**
   * Formulaire de création / édition.
   */
  public static function form(Form $form): Form
  {
    return $form
      ->schema([
        Forms\Components\Section::make('Informations')
          ->schema([
            Forms\Components\TextInput::make('project_name')
              ->label('Nom du projet')
              ->required()
              ->maxLength(255),
            Forms\Components\Textarea::make('project_description')
              ->label('Description')
              ->rows(4)
              ->columnSpanFull(),
            Forms\Components\Select::make('status_id')
              ->label('Statut')
              ->relationship('status', 'status_name')
              ->searchable()
              ->preload(),
            Forms\Components\Select::make('user_id')
              ->label('Responsable')
              ->relationship('user', 'email')
              ->getOptionLabelFromRecordUsing(fn ($record) => $record->name)
              ->searchable(['firstname', 'lastname', 'email'])
              ->preload(),
          ])
          ->columns(2),
        Forms\Components\Section::make('Liens & média')
          ->schema([
            Forms\Components\FileUpload::make('logo_url')
              ->label('Logo')
              ->image()
              ->disk('public')
              ->directory('images/projects')
              ->visibility('public')
              ->imageEditor(),
            Forms\Components\TextInput::make('web_url')
              ->label('Site web')
              ->url()
              ->maxLength(255),
            Forms\Components\TextInput::make('android_url')
              ->label('Android')
              ->url()
              ->maxLength(255),
            Forms\Components\TextInput::make('ios_url')
              ->label('iOS')
              ->url()
              ->maxLength(255),
          ])
          ->columns(2),
      ]);
  }

  /**
   * Tableau de liste des projets.
   */
  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        Tables\Columns\ImageColumn::make('logo_url')
          ->label('Logo')
          ->disk('public')
          ->circular(),
        Tables\Columns\TextColumn::make('project_name')
          ->label('Projet')
          ->searchable()
          ->sortable(),
        Tables\Columns\TextColumn::make('status.status_name')
          ->label('Statut')
          ->badge(),
        Tables\Columns\TextColumn::make('user.name')
          ->label('Responsable')
          ->toggleable(),
        Tables\Columns\TextColumn::make('created_at')
          ->label('Créé le')
          ->dateTime('d/m/Y')
          ->sortable(),
      ])
      ->defaultSort('created_at', 'desc')
      ->filters([
        Tables\Filters\SelectFilter::make('status_id')
          ->label('Statut')
          ->relationship('status', 'status_name'),
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
      'index' => Pages\ListProjects::route('/'),
      'create' => Pages\CreateProject::route('/create'),
      'edit' => Pages\EditProject::route('/{record}/edit'),
    ];
  }
}
