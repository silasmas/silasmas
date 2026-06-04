<?php

namespace App\Http\Controllers\API;

use App\Models\SiteBlock;
use App\Models\SiteSetting;

/**
 * API publique du contenu dynamique du site vitrine.
 */
class SiteController extends BaseController
{
  /**
   * Retourne le contenu structuré pour le front Next.js.
   */
  public function index()
  {
    $blocks = SiteBlock::query()->published()->get()->groupBy('group');

    $aboutBlock = $blocks->get('about')?->first();

    return $this->handleResponse([
      'about' => $aboutBlock ? [
        'eyebrow' => $aboutBlock->subtitle,
        'title' => $aboutBlock->title,
        'body' => $aboutBlock->body,
        'secondary_body' => $aboutBlock->secondary_body,
        'image' => $aboutBlock->imageUrl(),
      ] : null,
      'skills' => ($blocks->get('skill') ?? collect())->map(fn (SiteBlock $block) => [
        'id' => $block->id,
        'name' => $block->title,
        'value' => (int) $block->level,
      ])->values()->all(),
      'services' => ($blocks->get('service') ?? collect())->map(fn (SiteBlock $block) => [
        'id' => $block->id,
        'title' => $block->title,
        'description' => $block->body,
        'icon' => $block->icon ?? 'globe',
      ])->values()->all(),
      'hero_taglines' => ($blocks->get('hero_tagline') ?? collect())
        ->pluck('title')
        ->values()
        ->all(),
      'settings' => SiteSetting::instance()->toApiArray(),
    ], 'Contenu du site trouvé');
  }
}
