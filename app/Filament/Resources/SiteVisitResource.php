<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SiteVisitResource\Pages;
use App\Models\SiteVisit;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

/**
 * Ressource Filament — journal détaillé des visites et clics site vitrine.
 */
class SiteVisitResource extends Resource
{
  protected static ?string $model = SiteVisit::class;

  protected static ?string $navigationIcon = 'heroicon-o-signal';

  protected static ?string $navigationGroup = 'Site vitrine';

  protected static ?int $navigationSort = 10;

  protected static ?string $modelLabel = 'Visite';

  protected static ?string $pluralModelLabel = 'Visites & clics';

  protected static ?string $navigationLabel = 'Journal des visites';

  /**
   * Lecture seule — pas de formulaire d'édition.
   */
  public static function form(Form $form): Form
  {
    return $form->schema([]);
  }

  /**
   * Tableau détaillé des événements analytics.
   */
  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        Tables\Columns\TextColumn::make('visited_at')
          ->label('Date / heure')
          ->dateTime('d/m/Y H:i')
          ->sortable(),
        Tables\Columns\TextColumn::make('event_type')
          ->label('Type')
          ->badge()
          ->color(fn (string $state): string => $state === 'click' ? 'warning' : 'success')
          ->formatStateUsing(fn (string $state): string => match ($state) {
            'page_view' => 'Visite',
            'click' => 'Clic',
            default => $state,
          }),
        Tables\Columns\TextColumn::make('path')
          ->label('Page')
          ->limit(40)
          ->searchable(),
        Tables\Columns\TextColumn::make('click_label')
          ->label('Clic')
          ->placeholder('—')
          ->limit(35)
          ->toggleable(),
        Tables\Columns\TextColumn::make('click_target')
          ->label('Cible')
          ->placeholder('—')
          ->limit(35)
          ->toggleable(isToggledHiddenByDefault: true),
        Tables\Columns\TextColumn::make('country_name')
          ->label('Pays')
          ->formatStateUsing(fn ($state, SiteVisit $record) => $state ?: $record->country_code)
          ->sortable(),
        Tables\Columns\TextColumn::make('country_code')
          ->label('Code')
          ->badge()
          ->toggleable(isToggledHiddenByDefault: true),
        Tables\Columns\TextColumn::make('referrer')
          ->label('Referrer')
          ->limit(30)
          ->placeholder('—')
          ->toggleable(isToggledHiddenByDefault: true),
      ])
      ->defaultSort('visited_at', 'desc')
      ->filters([
        Tables\Filters\SelectFilter::make('event_type')
          ->label('Type')
          ->options([
            'page_view' => 'Visite',
            'click' => 'Clic',
          ]),
        Tables\Filters\SelectFilter::make('country_code')
          ->label('Pays')
          ->options(fn (): array => SiteVisit::query()
            ->select('country_code', 'country_name')
            ->distinct()
            ->orderBy('country_name')
            ->get()
            ->mapWithKeys(fn (SiteVisit $visit) => [
              $visit->country_code => ($visit->country_name ?: $visit->country_code),
            ])
            ->all()),
        Tables\Filters\Filter::make('visited_at')
          ->form([
            Forms\Components\DatePicker::make('from')
              ->label('Du'),
            Forms\Components\DatePicker::make('until')
              ->label('Au'),
          ])
          ->query(function ($query, array $data) {
            return $query
              ->when($data['from'], fn ($q, $date) => $q->whereDate('visited_at', '>=', $date))
              ->when($data['until'], fn ($q, $date) => $q->whereDate('visited_at', '<=', $date));
          }),
      ])
      ->actions([])
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
      'index' => Pages\ListSiteVisits::route('/'),
    ];
  }

  /**
   * Pas de création manuelle.
   */
  public static function canCreate(): bool
  {
    return false;
  }
}
