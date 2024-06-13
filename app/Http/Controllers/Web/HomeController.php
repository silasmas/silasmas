<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\ApiClientManager;
use App\Http\Controllers\Controller;
use App\Models\Project;

/**
 * @author Xanders
 * @see https://www.linkedin.com/in/xanders-samoth-b2770737/
 */
class HomeController extends Controller
{
    public static $api_client_manager;

    public function __construct()
    {
        $this::$api_client_manager = new ApiClientManager();
    }

    // ==================================== HTTP GET METHODS ====================================
    /**
     * GET: Welcome page
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $portofolio = Project::get();
        return view('welcome', compact("portofolio"));
    }
    public function detail($id)
    {
        $i=$id;
        return view('portfolio-details',compact("i"));
    }

    /**
     * GET: Generate symbolic link
     *
     * @return \Illuminate\View\View
     */
    public function symlink()
    {
        return view('symlink');
    }
}
