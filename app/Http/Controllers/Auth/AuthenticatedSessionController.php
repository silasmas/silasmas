<?php

namespace App\Http\Controllers\Auth;

use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use App\Http\Controllers\ApiClientManager;

/**
 * @author Xanders
 * @see https://www.linkedin.com/in/xanders-samoth-b2770737/
 */
class AuthenticatedSessionController extends Controller
{
    public static $api_client_manager;

    public function __construct()
    {
        $this::$api_client_manager = new ApiClientManager();
    }

    /**
     * Display the login view.
     */
    public function create(): View
    {
        // Find all user by role API URL
        $user_by_role = (!empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/api/user/find_by_role/Administrateur';
        // Find all user by role API calling
        $users = $this::$api_client_manager->call('GET', $user_by_role);

        Session::put('url.intended', URL::previous());

        return view('auth.login', ['users' => $users]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function login(Request $request): RedirectResponse|View
    {
        // Get inputs
        $inputs = [
            'username' => $request->username,
            'password' => $request->password
        ];
        // Login user API URL
        $url_user = (!empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/api/user/login';
        // Login user API calling
        $user = $this::$api_client_manager->call('POST', $url_user, $inputs);

        if ($user->success) {
            // Authentication datas (E-mail or Phone number)
            $auth_phone = Auth::attempt(['phone' => $user->data->phone, 'password' => $inputs['password']], $request->remember);
            $auth_email = Auth::attempt(['email' => $user->data->email, 'password' => $inputs['password']], $request->remember);

            if ($auth_phone || $auth_email) {
                $request->session()->regenerate();

                return Redirect::to(Session::get('url.intended'));
            }

        } else {
            return view('auth.login', [
                'inputs' => $inputs,
                'response_error' => $user
            ]);
        }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
