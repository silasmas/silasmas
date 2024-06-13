<?php

namespace App\Http\Controllers\Auth;

use stdClass;
use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use App\Http\Controllers\ApiClientManager;

/**
 * @author Xanders
 * @see https://www.linkedin.com/in/xanders-samoth-b2770737/
 */
class RegisteredUserController extends Controller
{
    public static $api_client_manager;

    public function __construct()
    {
        $this::$api_client_manager = new ApiClientManager();
    }

    /**
     * Display the registration view.
     */
    public function create(): View
    {
        // Find all user by role API URL
        $user_by_role = (!empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/api/user/find_by_role/Administrateur';
        // Find all user by role API calling
        $users = $this::$api_client_manager->call('GET', $user_by_role);

        if ($users->success) {
            abort(403);

        } else {
            return view('auth.register');
        }
}

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse|View
    {
        $inputs = [
            'firstname' => $request->register_firstname,
            'lastname' => $request->register_lastname,
            'surname' => $request->register_surname,
            'gender' => $request->register_gender,
            'birthdate' => explode('/', $request->register_birthdate)[2] . '-' . explode('/', $request->register_birthdate)[1] . '-' . explode('/', $request->register_birthdate)[0],
            'phone' => $request->register_phone,
            'email' => $request->register_email,
            'password' => $request->register_password,
            'confirm_password' => $request->confirm_password,
            'status_id' => $request->status_id,
            'roles_ids' => $request->roles_ids
        ];
        // Register user API URL
        $url_user = (!empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/api/user';
        // Register user API calling
        $user = $this::$api_client_manager->call('POST', $url_user, $inputs);

        if (trim($inputs['password']) == null) {
            $response_error = new stdClass();

            $response_error->message = $inputs['password'];
            $response_error->data = 'Le mot de passe est obligatoire';

            return view('auth.register', [
                'inputs' => $inputs,
                'response_error' => $response_error
            ]);
        }

        if ($user->success) {
            if ($user->data->phone != null) {
                if (Auth::attempt(['phone' => $user->data->phone, 'password' => $inputs['password']])) {
                    $request->session()->regenerate();

                    return Redirect::to('/admin');
                }
            }

            if ($user->data->email != null) {
                if (Auth::attempt(['email' => $user->data->email, 'password' => $inputs['password']])) {
                    $request->session()->regenerate();

                    return Redirect::to('/admin');
                }
            }

        } else {
            return view('auth.register', [
                'inputs' => $inputs,
                'response_error' => $user
            ]);
        }
    }
}
