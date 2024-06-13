<?php

namespace App\Http\Controllers\API;

use App\Models\Project;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\Project as ResourcesProject;

/**
 * @author Xanders
 * @see https://www.linkedin.com/in/xanders-samoth-b2770737/
 */
class ProjectController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $projects = Project::orderByDesc('created_at')->get();

        return $this->handleResponse(ResourcesProject::collection($projects), 'Projets trouvés');
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
            'project_name' => $request->project_name,
            'project_description' => $request->project_description,
            'web_url' => $request->web_url,
            'android_url' => $request->android_url,
            'ios_url' => $request->ios_url,
            'logo_url' => $request->logo_url,
            'status_id' => $request->status_id,
            'user_id' => $request->user_id,
        ];
        // Select all projects to check unique constraint
        $projects = Project::all();

        // Validate required fields
        if ($inputs['project_name'] == null OR $inputs['project_name'] == ' ') {
            return $this->handleError($inputs['project_name'], 'Champ obligatoire', 400);
        }

        // Check if project name already exists
        foreach ($projects as $another_project):
            if ($another_project->project_name == $inputs['project_name']) {
                return $this->handleError($inputs['project_name'], 'Ce projet existe déjà', 400);
            }
        endforeach;

        $project = Project::create($inputs);

        if ($inputs['logo_url'] != null) {
            $replace = substr($inputs['logo_url'], 0, strpos($inputs['logo_url'], ',') + 1);
            // Find substring from replace here eg: data:image/png;base64,
            $image = str_replace($replace, '', $inputs['logo_url']);
            $image = str_replace(' ', '+', $image);
            // Create image URL
            $image_url = 'images/projects/' . $project->id . '/' . Str::random(50) . '.png';

            // Upload image
            Storage::url(Storage::disk('public')->put($image_url, base64_decode($image)));

            $project->update([
                'logo_url' => '/storage/' . $image_url,
                'updated_at' => now()
            ]);
        }

        return $this->handleResponse(new ResourcesProject($project), 'Projet enregistré');
    }

    /**
     * Display the specified resource.
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $role = Project::find($id);

        if (is_null($role)) {
            return $this->handleError('Projet non trouvé');
        }

        return $this->handleResponse(new ResourcesProject($role), 'Projet trouvé');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $project)
    {
        // Get inputs
        $inputs = [
            'id' => $request->id,
            'project_name' => $request->project_name,
            'project_description' => $request->project_description,
            'web_url' => $request->web_url,
            'android_url' => $request->android_url,
            'ios_url' => $request->ios_url,
            'logo_url' => $request->logo_url,
            'status_id' => $request->status_id,
            'user_id' => $request->user_id
        ];
        // Select all projects to check unique constraint
        $projects = Project::all();
        $current_project = Project::find($inputs['id']);

        // Validate required fields
        if ($inputs['project_name'] == null OR $inputs['project_name'] == ' ') {
            return $this->handleError($inputs['project_name'], 'Champ obligatoire', 400);
        }

        // Check if project name already exists
        foreach ($projects as $another_project):
            if ($current_project->project_name != $inputs['project_name']) {
                if ($another_project->project_name == $inputs['project_name']) {
                    return $this->handleError($inputs['project_name'], 'Ce projet existe déjà', 400);
                }
            }
        endforeach;

        if ($inputs['project_name'] != null) {
            $project->update([
                'project_name' => $request->project_name,
                'updated_at' => now(),
            ]);
        }

        if ($inputs['project_description'] != null) {
            $project->update([
                'project_description' => $request->project_description,
                'updated_at' => now(),
            ]);
        }

        if ($inputs['web_url'] != null) {
            $project->update([
                'web_url' => $request->web_url,
                'updated_at' => now(),
            ]);
        }

        if ($inputs['android_url'] != null) {
            $project->update([
                'android_url' => $request->android_url,
                'updated_at' => now(),
            ]);
        }

        if ($inputs['ios_url'] != null) {
            $project->update([
                'ios_url' => $request->ios_url,
                'updated_at' => now(),
            ]);
        }

        if ($inputs['logo_url'] != null) {
            $replace = substr($inputs['logo_url'], 0, strpos($inputs['logo_url'], ',') + 1);
            // Find substring from replace here eg: data:image/png;base64,
            $image = str_replace($replace, '', $inputs['logo_url']);
            $image = str_replace(' ', '+', $image);
            $file = new Filesystem;

            // Clean "projects" directory
            $file->cleanDirectory($_SERVER['DOCUMENT_ROOT'] . '/storage/images/projects/' . $project->id);

            // Create image URL
            $image_url = 'images/projects/' . $project->id . '/' . Str::random(50) . '.png';

            // Upload image
            Storage::url(Storage::disk('public')->put($image_url, base64_decode($image)));

            $project->update([
                'logo_url' => '/storage/' . $image_url,
                'updated_at' => now()
            ]);
        }

        if ($inputs['status_id'] != null) {
            $project->update([
                'status_id' => $request->status_id,
                'updated_at' => now(),
            ]);
        }

        if ($inputs['user_id'] != null) {
            $project->update([
                'user_id' => $request->user_id,
                'updated_at' => now(),
            ]);
        }

        return $this->handleResponse(new ResourcesProject($project), 'Projet enregistré');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        $project->delete();

        $projects = Project::all();

        return $this->handleResponse(ResourcesProject::collection($projects), 'Projet retiré');
    }

    // ==================================== CUSTOM METHODS ====================================
    /**
     * Search a project by its name.
     *
     * @param  string $data
     * @return \Illuminate\Http\Response
     */
    public function search($data)
    {
        $projects = Project::where('project_name', 'LIKE', '%' . $data . '%')->get();

        if (is_null($projects)) {
            return $this->handleResponse(null, 'Aucun projet trouvé');
        }

        return $this->handleResponse(ResourcesProject::collection($projects), 'Résultat de la recherche');
    }

    /**
     * Search all projects having a specific status
     *
     * @param  string $status_name
     * @return \Illuminate\Http\Response
     */
    public function findByStatus($status_name)
    {
        $projects = Project::whereHas('status', function ($query) use ($status_name) {
                                    $query->where('status_name', $status_name);
                                })->orderByDesc('created_at')->get();

        if ($projects->count() == 0) {
            return $this->handleError(null, 'Aucun projet trouvé');
        }

        return $this->handleResponse(ResourcesProject::collection($projects), 'Projets trouvés');
    }
}
