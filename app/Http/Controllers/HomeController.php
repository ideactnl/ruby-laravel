<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * The controller's middleware.
     *
     * @var array
     */
    public static $middleware = [
        ['auth'],
    ];

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = Auth::user();
        $role = $user->getRoleNames()->first();

        return view('dashboard', [
            'user' => $user,
            'role' => $role,
        ]);
    }
}
