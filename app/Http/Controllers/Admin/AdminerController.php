<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminerController extends Controller
{
    /**
     * Launch the integrated Adminer interface natively in Laravel.
     */
    public function index(Request $request)
    {
        if ($request->has('logout')) {
            $request->session()->forget('adminer_verified_until');

            return redirect('/dashboard');
        }

        error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);
        ini_set('display_errors', '0');

        require_once app_path('Support/Adminer/boot.php');

        require_once app_path('Support/Adminer/core.php');

        return response('', 200);
    }
}
