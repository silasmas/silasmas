<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController;
use App\Http\Requests\StoreSiteVisitRequest;
use App\Models\SiteVisit;
use App\Support\VisitorCountry;

/**
 * API publique — enregistrement des visites et clics du site vitrine.
 */
class SiteAnalyticsController extends BaseController
{
  /**
   * Enregistre une visite de page ou un clic.
   */
  public function track(StoreSiteVisitRequest $request)
  {
    $country = VisitorCountry::fromRequest($request);

    SiteVisit::query()->create([
      'event_type' => $request->string('event_type')->toString(),
      'path' => $request->string('path')->toString(),
      'page_title' => $request->input('page_title'),
      'click_label' => $request->input('click_label'),
      'click_target' => $request->input('click_target'),
      'country_code' => $country['code'],
      'country_name' => $country['name'],
      'visited_at' => $request->input('visited_at') ?? now(),
      'visitor_key' => $request->input('visitor_key'),
      'referrer' => $request->input('referrer'),
    ]);

    return $this->handleResponse(null, 'Événement enregistré', 201);
  }
}
