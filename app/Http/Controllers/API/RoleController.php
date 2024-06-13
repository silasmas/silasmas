<?php

namespace App\Http\Controllers\API;

use App\Models\Role;
use Illuminate\Http\Request;
use App\Http\Resources\Role as ResourcesRole;

/**
 * @author Xanders
 * @see https://www.linkedin.com/in/xanders-samoth-b2770737/
 */
class RoleController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $roles = Role::all();

        return $this->handleResponse(ResourcesRole::collection($roles), 'Roles trouvés');
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
            'role_name' => $request->role_name,
            'role_description' => $request->role_description
        ];
        // Select all roles to check unique constraint
        $roles = Role::all();

        // Validate required fields
        if ($inputs['role_name'] == null OR $inputs['role_name'] == ' ') {
            return $this->handleError($inputs['role_name'], 'Champ obligatoire', 400);
        }

        // Check if role name already exists
        foreach ($roles as $another_role):
            if ($another_role->role_name == $inputs['role_name']) {
                return $this->handleError($inputs['role_name'], 'Ce rôle existe déjà', 400);
            }
        endforeach;

        $role = Role::create($inputs);

        return $this->handleResponse(new ResourcesRole($role), 'Rôle enregistré');
    }

    /**
     * Display the specified resource.
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $role = Role::find($id);

        if (is_null($role)) {
            return $this->handleError('Rôle non trouvé');
        }

        return $this->handleResponse(new ResourcesRole($role), 'Rôle trouvé');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Role $role)
    {
        // Get inputs
        $inputs = [
            'id' => $request->id,
            'role_name' => $request->role_name,
            'role_description' => $request->role_description,
            'updated_at' => now()
        ];
        // Select all roles and specific role to check unique constraint
        $roles = Role::all();
        $current_role = Role::find($inputs['id']);

        if ($inputs['role_name'] == null OR $inputs['role_name'] == ' ') {
            return $this->handleError($inputs['role_name'], 'Champ obligatoire', 400);
        }

        foreach ($roles as $another_role):
            if ($current_role->role_name != $inputs['role_name']) {
                if ($another_role->role_name == $inputs['role_name']) {
                    return $this->handleError($inputs['role_name'], 'Ce rôle existe déjà', 400);
                }
            }
        endforeach;

        $role->update($inputs);

        return $this->handleResponse(new ResourcesRole($role), 'Rôle modifié');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        $role->delete();

        $roles = Role::all();

        return $this->handleResponse(ResourcesRole::collection($roles), 'Rôle supprimé');
    }

    // ==================================== CUSTOM METHODS ====================================
    /**
     * Search a role by its name.
     *
     * @param  string $data
     * @return \Illuminate\Http\Response
     */
    public function search($data)
    {
        $roles = Role::where('role_name', 'LIKE', '%' . $data . '%')->get();

        if (is_null($roles)) {
            return $this->handleResponse(null, 'Aucun rôle trouvé');
        }

        return $this->handleResponse(ResourcesRole::collection($roles), 'Résultat de la recherche');
    }
}
