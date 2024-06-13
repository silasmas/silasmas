<?php

namespace App\Http\Controllers\API;

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\User as ResourcesUser;

/**
 * @author Xanders
 * @see https://www.linkedin.com/in/xanders-samoth-b2770737/
 */
class UserController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::orderByDesc('created_at')->get();

        return $this->handleResponse(ResourcesUser::collection($users), 'Utilisateurs trouvés');
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
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'surname' => $request->surname,
            'gender' => $request->gender,
            'birthdate' => $request->birthdate,
            'phone' => $request->phone,
            'email' => $request->email,
            'profile_description' => $request->profile_description,
            'password' => $request->password,
            'status_id' => $request->status_id
        ];
        $users = User::all();

        // Validate required fields
        if ($inputs['firstname'] == null AND $inputs['firstname'] == ' ') {
            return $this->handleError($inputs['firstname'], 'Donnez votre prénom');
        }

        if ($inputs['email'] == null AND $inputs['phone'] == null) {
            return $this->handleError($inputs['email'], 'Donnez l\'e-mail ou le n° de téléphone');
        }

        if ($inputs['email'] == ' ' AND $inputs['phone'] == ' ') {
            return $this->handleError($inputs['email'], 'Donnez l\'e-mail ou le n° de téléphone');
        }

        if ($inputs['email'] == null AND $inputs['phone'] == ' ') {
            return $this->handleError($inputs['email'], 'Donnez l\'e-mail ou le n° de téléphone');
        }

        if ($inputs['email'] == ' ' AND $inputs['phone'] == null) {
            return $this->handleError($inputs['email'], 'Donnez l\'e-mail ou le n° de téléphone');
        }

        if ($inputs['password'] != null OR $inputs['password'] != ' ') {
            if ($request->confirm_password != $inputs['password']) {
                return $this->handleError($request->confirm_password, 'Veuillez confirmer le mot de passe', 400);
            }
    
            if (preg_match('#^\S*(?=\S{8,})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])\S*$#', $inputs['password']) == 0) {
                return $this->handleError($inputs['password'], 'Il faut 8 caractères alphanumériques avec au moins une lettre majuscule et au moins un caractère spécial', 400);
            }    
        }

        if ($inputs['email'] != null) {
            // Check if user email already exists
            foreach ($users as $another_user):
                if ($another_user->email == $inputs['email']) {
                    return $this->handleError($inputs['email'], 'Cette adresse e-mail exite déjà', 400);
                }
            endforeach;
        }

        if ($inputs['phone'] != null) {
            // Check if user phone already exists
            foreach ($users as $another_user):
                if ($another_user->phone == $inputs['phone']) {
                    return $this->handleError($inputs['phone'], 'Ce n° de téléphone existe déjà', 400);
                }
            endforeach;
        }

        $user = User::create($inputs);

        if ($request->roles_ids != null) {
            $user->roles()->attach($request->roles_ids);
        }

        return $this->handleResponse($user, 'Utilisateur enregistré');
    }

    /**
     * Display the specified resource.
     * 
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::find($id);

        if (is_null($user)) {
            return $this->handleError('Utilisateur non trouvé');
        }

        return $this->handleResponse(new ResourcesUser($user), 'Utilisateur trouvé');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        // Get inputs
        $inputs = [
            'id' => $request->id,
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'surname' => $request->surname,
            'gender' => $request->gender,
            'birthdate' => $request->birthdate,
            'phone' => $request->phone,
            'email' => $request->email,
            'profile_description' => $request->profile_description,
            'password' => $request->password,
            'confirm_password' => $request->confirm_password,
            'status_id' => $request->status_id
        ];
        $users = User::all();

        if ($inputs['firstname'] != null) {
            $user->update([
                'firstname' => $request->firstname,
                'updated_at' => now(),
            ]);
        }

        if ($inputs['lastname'] != null) {
            $user->update([
                'lastname' => $request->lastname,
                'updated_at' => now(),
            ]);
        }

        if ($inputs['surname'] != null) {
            $user->update([
                'surname' => $request->surname,
                'updated_at' => now(),
            ]);
        }

        if ($inputs['gender'] != null) {
            $user->update([
                'gender' => $request->gender,
                'updated_at' => now(),
            ]);
        }

        if ($inputs['birthdate'] != null) {
            $user->update([
                'birthdate' => $request->birthdate,
                'updated_at' => now(),
            ]);
        }

        if ($inputs['phone'] != null) {
            // Check if user phone already exists
            foreach ($users as $another_user):
                if ($user->phone != $inputs['phone']) {
                    if ($another_user->phone == $inputs['phone']) {
                        return $this->handleError($inputs['phone'], 'Ce n° de téléphone existe déjà', 400);
                    }
                }
            endforeach;

            $user->update([
                'phone' => $request->phone,
                'updated_at' => now(),
            ]);
        }

        if ($inputs['email'] != null) {
            // Check if user phone already exists
            foreach ($users as $another_user):
                if ($user->email != $inputs['email']) {
                    if ($another_user->email == $inputs['email']) {
                        return $this->handleError($inputs['email'], 'Cette adresse e-mail existe déjà', 400);
                    }
                }
            endforeach;

            $user->update([
                'email' => $request->email,
                'updated_at' => now(),
            ]);
        }

        if ($inputs['profile_description'] != null) {
            $user->update([
                'profile_description' => $request->profile_description,
                'updated_at' => now(),
            ]);
        }

        if ($inputs['password'] != null) {
            if ($inputs['confirm_password'] != $inputs['password'] OR $inputs['confirm_password'] == null) {
                return $this->handleError($inputs['confirm_password'], 'Veuillez confirmer le mot de passe', 400);
            }
    
            if (preg_match('#^\S*(?=\S{8,})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])\S*$#', $inputs['password']) == 0) {
                return $this->handleError($inputs['password'], 'Il faut 8 caractères alphanumériques avec au moins une lettre majuscule et au moins un caractère spécial', 400);
            }

            $inputs['password'] = Hash::make($inputs['password']);

            $user->update([
                'password' => $inputs['password'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['status_id'] != null) {
            $user->update([
                'status_id' => $request->status_id,
                'updated_at' => now(),
            ]);
        }

        if ($request->roles_ids != null) {
            // Check if user role already exists
            foreach ($request->roles_ids as $role_id):
                if ( $user->roles()->contains($role_id)) {
                    $role = Role::find($role_id);

                    return $this->handleError($role_id, 'Le rôle "' . $role->role_name . '" existe déjà', 400);
                }
            endforeach;

            $user->roles()->sync($request->roles_ids, false);
        }

        return $this->handleResponse(new ResourcesUser($user), 'Utilisateur modifié');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $user->delete();

        $users = User::orderByDesc('created_at')->get();

        return $this->handleResponse(ResourcesUser::collection($users), 'Utilisateur retiré');
    }

    // ==================================== CUSTOM METHODS ====================================
    /**
     * Search a user by firstname / lastname / surname.
     *
     * @param  string $data
     * @return \Illuminate\Http\Response
     */
    public function search($data)
    {
        $users = User::where('firstname', 'LIKE', '%'. $data . '%')->orWhere('lastname', 'LIKE', '%'. $data . '%')->orWhere('surname', 'LIKE', '%'. $data . '%')->get();

        if (is_null($users)) {
            return $this->handleResponse(null, 'Aucun utilisateur trouvé');
        }

        return $this->handleResponse(ResourcesUser::collection($users), 'Résultat de la recherche');
    }

    /**
     * Search all users having a specific role
     *
     * @param  string $role_name
     * @return \Illuminate\Http\Response
     */
    public function findByRole($role_name)
    {
        $users = User::whereHas('roles', function ($query) use ($role_name) {
                                    $query->where('role_name', $role_name);
                                })->orderByDesc('created_at')->get();

        if ($users->count() == 0) {
            return $this->handleError(null, 'Aucun utilisateur trouvé');
        }

        return $this->handleResponse(ResourcesUser::collection($users), 'Utilisateurs trouvés');
    }

    /**
     * Search all users having a role different than the given
     *
     * @param  string $role_name
     * @return \Illuminate\Http\Response
     */
    public function findByNotRole($role_name)
    {
        $users = User::whereDoesntHave('roles', function ($query) use ($role_name) {
                                    $query->where('role_name', $role_name);
                                })->orderByDesc('created_at')->get();

        if ($users->count() == 0) {
            return $this->handleError(null, 'Aucun utilisateur trouvé');
        }

        return $this->handleResponse(ResourcesUser::collection($users), 'Utilisateurs trouvés');    
    }

    /**
     * Search all users having specific status.
     *
     * @param  int $status_id
     * @return \Illuminate\Http\Response
     */
    public function findByStatus($status_id)
    {
        $users = User::where('status_id', $status_id)->orderByDesc('created_at')->get();

        if (is_null($users)) {
            return $this->handleResponse(null, 'Aucun utilisateur trouvé');
        }

        return $this->handleResponse(ResourcesUser::collection($users), 'Utilisateurs trouvés');
    }

    /**
     * Handle an incoming authentication request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        // Get inputs
        $inputs = [
            'username' => $request->username,
            'password' => $request->password
        ];

        if ($inputs['username'] == null OR $inputs['username'] == ' ') {
            return $this->handleError($inputs['username'], 'Champ obligatoire', 400);
        }

        if ($inputs['password'] == null) {
            return $this->handleError($inputs['password'], 'Champ obligatoire', 400);
        }

        if (is_numeric($inputs['username'])) {
            $user = User::where('phone', $inputs['username'])->first();

            if (!$user) {
                return $this->handleError($inputs['username'], 'Utilisateur inconnu', 400);
            }

            if (!Hash::check($inputs['password'], $user->password)) {
                return $this->handleError($inputs['password'], 'Mot de passe incorrect', 400);
            }

            return $this->handleResponse(new ResourcesUser($user), 'Utilisateur trouvé');

        } else {
            $user = User::where('email', $inputs['username'])->first();

            if (!$user) {
                return $this->handleError($inputs['username'], 'Utilisateur inconnu', 400);
            }

            if (!Hash::check($inputs['password'], $user->password)) {
                return $this->handleError($inputs['password'], 'Mot de passe incorrect', 400);
            }

            return $this->handleResponse(new ResourcesUser($user), 'Utilisateur trouvé');
        }
    }

    /**
     * Withdraw user roles in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function withdrawRole(Request $request, $id)
    {
        $user = User::find($id);

        if ($request->roles_ids != null) {
            $user->roles()->detach($request->roles_ids);

            return $this->handleResponse(new ResourcesUser($user), count($request->roles_ids) > 1 ? 'Rôles rétirés' : 'Rôle rétiré');
        }

        if ($request->role_id != null) {
            $user->roles()->detach($request->role_id);

            return $this->handleResponse(new ResourcesUser($user), 'Rôle rétiré');
        }
    }

    /**
     * Update user password in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function updatePassword(Request $request, $id)
    {
        // Get inputs
        $inputs = [
            'former_password' => $request->former_password,
            'new_password' => $request->new_password,
            'confirm_new_password' => $request->confirm_new_password
        ];
        $user = User::find($id);

        if ($inputs['former_password'] == null) {
            return $this->handleError($inputs['former_password'], 'Veuillez donner l\'ancien mot de passe', 400);
        }

        if ($inputs['new_password'] == null) {
            return $this->handleError($inputs['new_password'], 'Champ obligatoire', 400);
        }

        if ($inputs['confirm_new_password'] == null) {
            return $this->handleError($inputs['confirm_new_password'], 'Veuillez confirmer le mot de passe', 400);
        }

        if (Hash::check($inputs['former_password'], $user->password) == false) {
            return $this->handleError($inputs['former_password'], 'Ancien mot de passe incorrect', 400);
        }

        if ($inputs['confirm_new_password'] != $inputs['new_password']) {
            return $this->handleError($inputs['confirm_new_password'], 'Veuillez confirmer le mot de passe', 400);
        }

        if (preg_match('#^\S*(?=\S{8,})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])\S*$#', $inputs['new_password']) == 0) {
            return $this->handleError($inputs['new_password'], 'Il faut 8 caractères alphanumériques avec au moins une lettre majuscule et au moins un caractère spécial', 400);
        }

        // update "password" column
        $user->update([
            'password' => Hash::make($inputs['new_password']),
            'updated_at' => now()
        ]);

        return $this->handleResponse(new ResourcesUser($user), 'Mot de passe modifié');
    }

    /**
     * Update user avatar picture in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function updateAvatarPicture(Request $request, $id)
    {
        $inputs = [
            'user_id' => $request->user_id,
            'image_64' => $request->image_64
        ];
        $replace = substr($inputs['image_64'], 0, strpos($inputs['image_64'], ',') + 1);
        // Find substring from replace here eg: data:image/png;base64,
        $image = str_replace($replace, '', $inputs['image_64']);
        $image = str_replace(' ', '+', $image);
        $file = new Filesystem;

        // Clean "users" directory
        $file->cleanDirectory($_SERVER['DOCUMENT_ROOT'] . '/storage/images/users/' . $inputs['user_id']);

        // Create image URL
		$image_url = 'images/users/' . $inputs['user_id'] . '/' . Str::random(50) . '.png';

		// Upload image
		Storage::url(Storage::disk('public')->put($image_url, base64_decode($image)));

		$user = User::find($id);

        $user->update([
            'avatar_url' => '/storage/' . $image_url,
            'updated_at' => now()
        ]);

        return $this->handleResponse(new ResourcesUser($user), 'Profil modifié');
    }
}
