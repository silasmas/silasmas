<?php

namespace App\Http\Controllers\API;

use App\Models\Website;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\Website as ResourcesWebsite;

/**
 * @author Xanders
 * @see https://www.linkedin.com/in/xanders-samoth-b2770737/
 */
class WebsiteController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $websites = Website::orderByDesc('created_at')->get();

        return $this->handleResponse(ResourcesWebsite::collection($websites), 'Sites web trouvés');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Get inputs
        $inputs = [
            'website_name' => $request->website_name,
            'website_url' => $request->website_url,
            'logo_url' => $request->logo_url,
            'icon' => $request->icon,
            'user_id' => $request->user_id
        ];
        // Select all websites to check unique constraint
        $websites = Website::all();

        // Validate required fields
        if ($inputs['website_url'] == null OR $inputs['website_url'] == ' ') {
            return $this->handleError($inputs['website_url'], 'Champ obligatoire', 400);
        }

        // Check if website URL already exists
        foreach ($websites as $another_website):
            if ($another_website->website_url == $inputs['website_url']) {
                return $this->handleError($inputs['website_url'], 'Ce lien de site web existe déjà', 400);
            }
        endforeach;

        $website = Website::create($inputs);

        if ($inputs['logo_url'] != null) {
            $replace = substr($inputs['logo_url'], 0, strpos($inputs['logo_url'], ',') + 1);
            // Find substring from replace here eg: data:image/png;base64,
            $image = str_replace($replace, '', $inputs['logo_url']);
            $image = str_replace(' ', '+', $image);
            // Create image URL
            $image_url = 'images/websites/' . $website->id . '/' . Str::random(50) . '.png';

            // Upload image
            Storage::url(Storage::disk('public')->put($image_url, base64_decode($image)));

            $website->update([
                'logo_url' => '/storage/' . $image_url,
                'updated_at' => now()
            ]);
        }

        return $this->handleResponse(new ResourcesWebsite($website), 'Site web enregistré');
    }

    /**
     * Display the specified resource.
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $website = Website::find($id);

        if (is_null($website)) {
            return $this->handleError('Site web non trouvé');
        }

        return $this->handleResponse(new ResourcesWebsite($website), 'Site web trouvé');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Website  $website
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Website $website)
    {
        // Get inputs
        $inputs = [
            'id' => $request->id,
            'website_name' => $request->website_name,
            'website_url' => $request->website_url,
            'logo_url' => $request->logo_url,
            'icon' => $request->icon,
            'user_id' => $request->user_id
        ];
        // Select all websites to check unique constraint
        $websites = Website::all();
        $current_website = Website::find($inputs['id']);

        // Validate required fields
        if ($inputs['website_url'] == null OR $inputs['website_url'] == ' ') {
            return $this->handleError($inputs['website_url'], 'Champ obligatoire', 400);
        }

        // Check if website URL already exists
        foreach ($websites as $another_website):
            if ($current_website->website_url != $inputs['website_url']) {
                if ($another_website->website_url == $inputs['website_url']) {
                    return $this->handleError($inputs['website_url'], 'Ce lien de site web existe déjà', 400);
                }
            }
        endforeach;

        if ($inputs['website_name'] != null) {
            $website->update([
                'website_name' => $request->website_name,
                'updated_at' => now(),
            ]);
        }

        if ($inputs['website_url'] != null) {
            $website->update([
                'website_url' => $request->website_url,
                'updated_at' => now(),
            ]);
        }

        if ($inputs['logo_url'] != null) {
            $replace = substr($inputs['logo_url'], 0, strpos($inputs['logo_url'], ',') + 1);
            // Find substring from replace here eg: data:image/png;base64,
            $image = str_replace($replace, '', $inputs['logo_url']);
            $image = str_replace(' ', '+', $image);
            $file = new Filesystem;

            // Clean "websites" directory
            $file->cleanDirectory($_SERVER['DOCUMENT_ROOT'] . '/storage/images/websites/' . $website->id);

            // Create image URL
            $image_url = 'images/websites/' . $website->id . '/' . Str::random(50) . '.png';

            // Upload image
            Storage::url(Storage::disk('public')->put($image_url, base64_decode($image)));

            $website->update([
                'logo_url' => '/storage/' . $image_url,
                'updated_at' => now()
            ]);
        }

        if ($inputs['icon'] != null) {
            $website->update([
                'icon' => $request->icon,
                'updated_at' => now(),
            ]);
        }

        if ($inputs['user_id'] != null) {
            $website->update([
                'user_id' => $request->user_id,
                'updated_at' => now(),
            ]);
        }

        return $this->handleResponse(new ResourcesWebsite($website), 'Site web modifié');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Website  $website
     * @return \Illuminate\Http\Response
     */
    public function destroy(Website $website)
    {
        $website->delete();

        $websites = Website::orderByDesc('created_at')->get();

        return $this->handleResponse(ResourcesWebsite::collection($websites), 'Site web supprimé');
    }
}
