<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SiteBlockResource\Pages;
use App\Models\SiteBlock;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

/**
 * Ressource Filament — contenu dynamique du site vitrine.
 */
class SiteBlockResource extends Resource
{
  protected static ?string $model = SiteBlock::class;

  protected static ?string $navigationIcon = 'heroicon-o-document-text';

  protected static ?string $navigationGroup = 'Site vitrine';

  protected static ?int $navigationSort = 0;

  protected static ?string $modelLabel = 'Bloc de contenu';

  protected static ?string $pluralModelLabel = 'Contenu du site';

  protected static ?string $navigationLabel = 'Contenu du site';

  /**
   * Formulaire de création / édition.
   */
  public static function form(Form $form): Form
  {
    return $form
      ->schema([
        Forms\Components\Select::make('group')
          ->label('Section')
          ->options([
            'about' => 'À propos',
            'skill' => 'Compétence',
            'service' => 'Service',
            'hero_tagline' => 'Expertise hero (rotation)',
          ])
          ->required()
          ->live(),
        Forms\Components\TextInput::make('title')
          ->label('Titre')
          ->required()
          ->maxLength(255)
          ->columnSpanFull(),
        Forms\Components\TextInput::make('subtitle')
          ->label('Sous-titre / eyebrow')
          ->maxLength(255)
          ->visible(fn (Get $get): bool => in_array($get('group'), ['about'], true))
          ->columnSpanFull(),
        Forms\Components\Textarea::make('body')
          ->label('Description')
          ->rows(4)
          ->visible(fn (Get $get): bool => in_array($get('group'), ['about', 'service'], true))
          ->columnSpanFull(),
        Forms\Components\Textarea::make('secondary_body')
          ->label('Texte secondaire')
          ->rows(3)
          ->visible(fn (Get $get): bool => $get('group') === 'about')
          ->columnSpanFull(),
        Forms\Components\Select::make('icon')
          ->label('Icône')
          ->options([
            'globe' => 'Globe (web)',
            'mobile' => 'Mobile',
            'marketing' => 'Marketing',
            'design' => 'Design',
          ])
          ->visible(fn (Get $get): bool => $get('group') === 'service'),
        Forms\Components\TextInput::make('level')
          ->label('Niveau (%)')
          ->numeric()
          ->minValue(0)
          ->maxValue(100)
          ->visible(fn (Get $get): bool => $get('group') === 'skill'),
        Forms\Components\FileUpload::make('image')
          ->label('Image')
          ->image()
          ->disk('public')
          ->directory('images/site')
          ->visibility('public')
          ->maxFiles(1)
          ->visible(fn (Get $get): bool => $get('group') === 'about'),
        Forms\Components\TextInput::make('sort_order')
          ->label('Ordre')
          ->numeric()
          ->default(0)
          ->minValue(0),
        Forms\Components\Toggle::make('is_published')
          ->label('Publié')
          ->default(true),
      ])
      ->columns(2);
  }

  /**
   * Tableau de liste des blocs.
   */
  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        Tables\Columns\TextColumn::make('group')
          ->label('Section')
          ->badge()
          ->formatStateUsing(fn (string $state): string => match ($state) {
            'about' => 'À propos',
            'skill' => 'Compétence',
            'service' => 'Service',
            'hero_tagline' => 'Hero',
            default => $state,
          }),
        Tables\Columns\ImageColumn::make('image')
          ->label('Image')
          ->disk('public')
          ->height(40)
          ->toggleable(),
        Tables\Columns\TextColumn::make('title')
          ->label('Titre')
          ->searchable()
          ->limit(50),
        Tables\Columns\TextColumn::make('level')
          ->label('%')
          ->suffix('%')
          ->toggleable(),
        Tables\Columns\IconColumn::make('is_published')
          ->label('Publié')
          ->boolean(),
        Tables\Columns\TextColumn::make('sort_order')
          ->label('Ordre')
          ->sortable(),
      ])
      ->defaultSort('sort_order')
      ->filters([
        Tables\Filters\SelectFilter::make('group')
          ->label('Section')
          ->options([
            'about' => 'À propos',
            'skill' => 'Compétence',
            'service' => 'Service',
            'hero_tagline' => 'Hero',
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
      'index' => Pages\ListSiteBlocks::route('/'),
      'create' => Pages\CreateSiteBlock::route('/create'),
      'edit' => Pages\EditSiteBlock::route('/{record}/edit'),
    ];
  }
}
