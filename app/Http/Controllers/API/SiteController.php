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
    $heroBlock = $blocks->get('hero')?->first();

    return $this->handleResponse([
      'hero' => $heroBlock ? [
        'eyebrow' => $heroBlock->subtitle,
        'headline' => $heroBlock->title,
        'headline_accent' => $heroBlock->secondary_body,
        'body' => $heroBlock->body,
        'image' => $heroBlock->imageUrl(),
      ] : null,
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
        'excerpt' => $block->subtitle,
        'icon' => $block->icon ?? 'globe',
      ])->values()->all(),
      'testimonials' => ($blocks->get('testimonial') ?? collect())->map(fn (SiteBlock $block) => [
        'id' => $block->id,
        'quote' => $block->body,
        'author' => $block->title,
        'role' => $block->subtitle,
      ])->values()->all(),
      'principles' => ($blocks->get('principle') ?? collect())->map(fn (SiteBlock $block) => [
        'id' => $block->id,
        'title' => $block->title,
        'body' => $block->body,
      ])->values()->all(),
      'faqs' => ($blocks->get('faq') ?? collect())->map(fn (SiteBlock $block) => [
        'id' => $block->id,
        'q' => $block->title,
        'a' => $block->body,
      ])->values()->all(),
      'client_logos' => ($blocks->get('client_logo') ?? collect())
        ->pluck('title')
        ->values()
        ->all(),
      'hero_taglines' => ($blocks->get('hero_tagline') ?? collect())
        ->pluck('title')
        ->values()
        ->all(),
      'silas_page' => static::buildSilasPage($blocks),
      'settings' => SiteSetting::instance()->toApiArray(),
    ], 'Contenu du site trouvé');
  }

  /**
   * Assemble le contenu structuré de la page /silas.
   *
   * @param \Illuminate\Support\Collection<string, \Illuminate\Support\Collection<int, SiteBlock>> $blocks
   * @return array<string, mixed>
   */
  protected static function buildSilasPage($blocks): array
  {
    $hero = $blocks->get('silas')?->first();
    $availability = $blocks->get('silas_availability')?->first();
    $journeyIntro = $blocks->get('silas_journey_intro')?->first();
    $banner = $blocks->get('silas_banner')?->first();
    $cta = $blocks->get('silas_cta')?->first();

    return [
      'hero' => $hero ? [
        'eyebrow' => $hero->subtitle,
        'title' => $hero->title,
        'accent' => $hero->secondary_body,
        'body' => $hero->body,
        'image' => $hero->imageUrl(),
      ] : null,
      'availability' => $availability ? [
        'title' => $availability->title,
        'body' => $availability->body,
      ] : null,
      'journey_intro' => $journeyIntro ? [
        'title' => $journeyIntro->title,
        'body' => $journeyIntro->body,
      ] : null,
      'journey' => ($blocks->get('silas_journey') ?? collect())->map(fn (SiteBlock $block) => [
        'id' => $block->id,
        'year' => $block->subtitle,
        'title' => $block->title,
        'body' => $block->body,
      ])->values()->all(),
      'banner' => $banner ? [
        'badge' => $banner->subtitle,
        'title' => $banner->title,
        'image' => $banner->imageUrl(),
      ] : null,
      'offers' => ($blocks->get('silas_offer') ?? collect())->map(fn (SiteBlock $block) => [
        'id' => $block->id,
        'icon' => $block->icon ?? 'compass',
        'title' => $block->title,
        'body' => $block->body,
      ])->values()->all(),
      'cta' => $cta ? [
        'title' => $cta->title,
        'subtitle' => $cta->body,
        'cta' => $cta->subtitle,
      ] : null,
    ];
  }
}
