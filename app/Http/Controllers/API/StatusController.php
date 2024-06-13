<?php

namespace App\Http\Controllers\API;

use App\Models\Status;
use Illuminate\Http\Request;
use App\Http\Resources\Status as ResourcesStatus;

/**
 * @author Xanders
 * @see https://www.linkedin.com/in/xanders-samoth-b2770737/
 */
class StatusController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $statuses = Status::all();

        return $this->handleResponse(ResourcesStatus::collection($statuses), 'Etats trouvés');
    }

    /**
     * Store a resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Get inputs
        $inputs = [
            'status_name' => $request->status_name,
            'status_description' => $request->status_description,
            'color' => $request->color
        ];
        // Select all statuses to check unique constraint
        $statuses = Status::all();

        // Validate required fields
        if ($inputs['status_name'] == null OR $inputs['status_name'] == ' ') {
            return $this->handleError($inputs['status_name'], 'Champ obligatoire', 400);
        }

        // Check if status name already exists
        foreach ($statuses as $another_status):
            if ($another_status->status_name == $inputs['status_name']) {
                return $this->handleError($inputs['status_name'], 'Ce nom d\'état existe déjà', 400);
            }
        endforeach;

        $status = Status::create($inputs);

        return $this->handleResponse(new ResourcesStatus($status), 'Etat créé');
    }

    /**
     * Display the specified resource.
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $status = Status::find($id);

        if (is_null($status)) {
            return $this->handleError('Etat non trouvé');
        }

        return $this->handleResponse(new ResourcesStatus($status), 'Etat trouvé');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Status  $status
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Status $status)
    {
        // Get inputs
        $inputs = [
            'id' => $request->id,
            'status_name' => $request->status_name,
            'status_description' => $request->status_description,
            'color' => $request->color,
            'updated_at' => now()
        ];
        // Select all statuses and specific status to check unique constraint
        $statuses = Status::all();
        $current_status = Status::find($inputs['id']);

        if ($inputs['status_name'] == null OR $inputs['status_name'] == ' ') {
            return $this->handleError($inputs['status_name'], 'Champ obligatoire', 400);
        }

        foreach ($statuses as $another_status):
            if ($current_status->status_name != $inputs['status_name']) {
                if ($another_status->status_name == $inputs['status_name']) {
                    return $this->handleError($inputs['status_name'], 'Ce nom d\'état existe déjà', 400);
                }
            }
        endforeach;

        $status->update($inputs);

        return $this->handleResponse(new ResourcesStatus($status), 'Etat modifié');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Status  $status
     * @return \Illuminate\Http\Response
     */
    public function destroy(Status $status)
    {
        $status->delete();

        $statuses = Status::all();

        return $this->handleResponse(ResourcesStatus::collection($statuses), 'Etat supprimé');
    }

    // ==================================== CUSTOM METHODS ====================================
    /**
     * Search a status by its name.
     *
     * @param  string $data
     * @return \Illuminate\Http\Response
     */
    public function search($data)
    {
        $statuses = Status::where('status_name', 'LIKE', '%' . $data . '%')->get();

        if (is_null($statuses)) {
            return $this->handleResponse(null, 'Aucun état trouvé');
        }

        return $this->handleResponse(new ResourcesStatus($statuses), 'Résultat de la recherche');
    }
}
