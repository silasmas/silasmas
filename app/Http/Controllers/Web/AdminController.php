<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Mail\message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

/**
 * @author Xanders
 * @see https://www.linkedin.com/in/xanders-samoth-b2770737/
 */
class AdminController extends Controller
{
    public static $api_client_manager;

    public function __construct()
    {
        // $this::$api_client_manager = new ApiClientManager();
    }
    public function sendMessage(Request $request)
    {
        $request->validate([
            'nom' => ['required', 'string', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255'],
            'message' => ['required', 'string'],
        ]);
        Mail::to("ir-masimango@silasmas.com")->send(new message($request->email, $request->nom, $request->subject, $request->message, $request->phone));
        return back()->with('msg', "Votre message est envoyer avec succès!");
    }
    // ==================================== HTTP GET METHODS ====================================
    /**
     * GET: Admin home page
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // [
        //     'messages' => $messages->data,
        //     'projects' => $projects->data,
        //     'collaborators' => $collaborators->data,
        // ]
        return view('dashboard');
    }

    /**
     * GET: Account page
     *
     * @return \Illuminate\View\View
     */
    public function account()
    {
        return view('admin.account');
    }

    /**
     * GET: Message page
     *
     * @return \Illuminate\View\View
     */
    public function message()
    {
        return view('admin.message');
    }

    /**
     * GET: Project page
     *
     * @return \Illuminate\View\View
     */
    public function project()
    {
        return view('admin.project');
    }

    /**
     * GET: Team page
     *
     * @return \Illuminate\View\View
     */
    public function team()
    {
        return view('admin.team');
    }

    // ==================================== HTTP POST METHODS ====================================
    /**
     * POST: Update account
     *
     * @return
     */
    public function updateAccount()
    {
        //
    }

    /**
     * POST: Add a project
     *
     * @return
     */
    public function addProject()
    {
        //
    }

    /**
     * POST: Add a team member
     *
     * @return
     */
    public function addMember()
    {
        //
    }
}
