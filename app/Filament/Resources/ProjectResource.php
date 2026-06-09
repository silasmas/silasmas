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
   * Catégories portfolio affichées sur le site.
   *
   * @return array<string, string>
   */
  public static function categoryOptions(): array
  {
    return [
      'Site Web' => 'Site Web',
      'Application' => 'Application',
      'Plateforme' => 'Plateforme',
      'Branding' => 'Branding',
    ];
  }

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
              ->maxLength(255)
              ->live(onBlur: true)
              ->afterStateUpdated(function (Forms\Set $set, ?string $state, Forms\Get $get): void {
                if (filled($get('slug'))) {
                  return;
                }

                if (filled($state)) {
                  $set('slug', \Illuminate\Support\Str::slug($state));
                }
              }),
            Forms\Components\TextInput::make('slug')
              ->label('Slug URL')
              ->helperText('Utilisé dans /portfolio/{slug}. Généré automatiquement si vide.')
              ->maxLength(255)
              ->unique(ignoreRecord: true),
            Forms\Components\TextInput::make('client_name')
              ->label('Client')
              ->maxLength(255),
            Forms\Components\Select::make('category')
              ->label('Catégorie')
              ->options(static::categoryOptions())
              ->searchable(),
            Forms\Components\TextInput::make('project_date')
              ->label('Date / année')
              ->placeholder('2025 ou Septembre 2025')
              ->maxLength(100),
            Forms\Components\Textarea::make('project_description')
              ->label('Résumé (extrait)')
              ->rows(3)
              ->helperText('Affiché sur les cartes portfolio et en introduction de l\'étude de cas.')
              ->columnSpanFull(),
            Forms\Components\Select::make('status_id')
              ->label('Statut interne')
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
        Forms\Components\Section::make('Étude de cas')
          ->description('Contenu détaillé de la page /portfolio/{slug}.')
          ->schema([
            Forms\Components\Textarea::make('context')
              ->label('Contexte')
              ->rows(4)
              ->columnSpanFull(),
            Forms\Components\Textarea::make('challenge')
              ->label('Enjeux')
              ->rows(4)
              ->columnSpanFull(),
            Forms\Components\Textarea::make('outcome')
              ->label('Résultat')
              ->rows(4)
              ->columnSpanFull(),
            Forms\Components\TagsInput::make('tags')
              ->label('Technologies / tags')
              ->placeholder('Next.js, Laravel…')
              ->columnSpanFull(),
            Forms\Components\Repeater::make('metrics')
              ->label('Indicateurs clés')
              ->schema([
                Forms\Components\TextInput::make('label')
                  ->label('Libellé')
                  ->required(),
                Forms\Components\TextInput::make('value')
                  ->label('Valeur')
                  ->required(),
              ])
              ->columns(2)
              ->defaultItems(0)
              ->columnSpanFull(),
          ])
          ->collapsed(false),
        Forms\Components\Section::make('Liens & média')
          ->schema([
            Forms\Components\FileUpload::make('logo_url')
              ->label('Image de couverture')
              ->image()
              ->disk('public')
              ->directory('images/projects')
              ->visibility('public')
              ->imageEditor(),
            Forms\Components\FileUpload::make('gallery_urls')
              ->label('Galerie')
              ->image()
              ->disk('public')
              ->directory('images/projects/gallery')
              ->visibility('public')
              ->multiple()
              ->reorderable()
              ->columnSpanFull(),
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
            Forms\Components\TextInput::make('sort_order')
              ->label('Ordre d\'affichage')
              ->numeric()
              ->default(0),
            Forms\Components\Toggle::make('is_published')
              ->label('Publié sur le site')
              ->default(true),
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
          ->label('Cover')
          ->disk('public')
          ->height(48)
          ->width(64),
        Tables\Columns\TextColumn::make('project_name')
          ->label('Projet')
          ->searchable()
          ->sortable(),
        Tables\Columns\TextColumn::make('slug')
          ->label('Slug')
          ->toggleable()
          ->copyable(),
        Tables\Columns\TextColumn::make('client_name')
          ->label('Client')
          ->toggleable(),
        Tables\Columns\TextColumn::make('category')
          ->label('Catégorie')
          ->badge(),
        Tables\Columns\IconColumn::make('is_published')
          ->label('Publié')
          ->boolean(),
        Tables\Columns\TextColumn::make('sort_order')
          ->label('Ordre')
          ->sortable(),
        Tables\Columns\TextColumn::make('created_at')
          ->label('Créé le')
          ->dateTime('d/m/Y')
          ->sortable()
          ->toggleable(isToggledHiddenByDefault: true),
      ])
      ->defaultSort('sort_order')
      ->reorderable('sort_order')
      ->filters([
        Tables\Filters\SelectFilter::make('category')
          ->label('Catégorie')
          ->options(static::categoryOptions()),
        Tables\Filters\TernaryFilter::make('is_published')
          ->label('Publié'),
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
