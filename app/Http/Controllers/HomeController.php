<?php

namespace App\Http\Controllers;

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
        return view('home');
    }
}
