<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SiteSettingResource\Pages;
use App\Models\SiteSetting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

/**
 * Ressource Filament — paramétrage global du site (singleton).
 */
class SiteSettingResource extends Resource
{
  protected static ?string $model = SiteSetting::class;

  protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

  protected static ?string $navigationGroup = 'Site vitrine';

  protected static ?int $navigationSort = -1;

  protected static ?string $modelLabel = 'Paramétrage';

  protected static ?string $pluralModelLabel = 'Paramétrage du site';

  protected static ?string $navigationLabel = 'Paramétrage du site';

  /**
   * Un seul enregistrement de paramétrage autorisé.
   */
  public static function canCreate(): bool
  {
    return self::getModel()::query()->count() === 0;
  }

  /**
   * Formulaire d'édition du paramétrage.
   */
  public static function form(Form $form): Form
  {
    return $form
      ->schema([
        Forms\Components\Section::make('Identité')
          ->schema([
            Forms\Components\TextInput::make('site_title')
              ->label('Titre du site')
              ->required()
              ->maxLength(255),
            Forms\Components\TextInput::make('site_tagline')
              ->label('Sous-titre / slogan')
              ->maxLength(255)
              ->columnSpanFull(),
            Forms\Components\FileUpload::make('logo')
              ->label('Logo')
              ->image()
              ->disk('public')
              ->directory('images/site/branding')
              ->visibility('public')
              ->maxFiles(1),
            Forms\Components\FileUpload::make('favicon')
              ->label('Favicon')
              ->image()
              ->disk('public')
              ->directory('images/site/branding')
              ->visibility('public')
              ->maxFiles(1)
              ->helperText('Format carré recommandé (PNG, ICO converti en PNG).'),
          ])
          ->columns(2),
        Forms\Components\Section::make('Contact')
          ->schema([
            Forms\Components\TextInput::make('email')
              ->label('E-mail')
              ->email()
              ->maxLength(255),
            Forms\Components\TextInput::make('phone_primary')
              ->label('Téléphone principal')
              ->maxLength(50),
            Forms\Components\TextInput::make('phone_secondary')
              ->label('Téléphone secondaire')
              ->maxLength(50),
            Forms\Components\TextInput::make('address')
              ->label('Adresse')
              ->maxLength(255)
              ->columnSpanFull(),
          ])
          ->columns(2),
        Forms\Components\Section::make('Pied de page')
          ->schema([
            Forms\Components\Textarea::make('footer_description')
              ->label('Texte de présentation')
              ->rows(4)
              ->columnSpanFull(),
          ]),
        Forms\Components\Section::make('Paiements & devises')
          ->description('Taux pour afficher automatiquement USD et CDF sur les sessions payantes.')
          ->schema([
            Forms\Components\TextInput::make('usd_to_cdf_rate')
              ->label('Taux de change (1 USD = X CDF)')
              ->numeric()
              ->minValue(1)
              ->step(0.01)
              ->helperText('Ex. 2850 — les inscrits verront le prix en USD et en franc congolais.'),
          ]),
      ]);
  }

  /**
   * Tableau (redirige vers l'unique fiche).
   */
  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        Tables\Columns\TextColumn::make('site_title')->label('Titre'),
        Tables\Columns\TextColumn::make('email')->label('E-mail'),
        Tables\Columns\TextColumn::make('updated_at')->label('Modifié le')->dateTime('d/m/Y H:i'),
      ])
      ->actions([
        Tables\Actions\EditAction::make(),
      ]);
  }

  /**
   * @return array<class-string>
   */
  public static function getPages(): array
  {
    return [
      'index' => Pages\ManageSiteSetting::route('/'),
      'edit' => Pages\EditSiteSetting::route('/{record}/edit'),
    ];
  }
}
