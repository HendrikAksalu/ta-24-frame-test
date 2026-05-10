<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    /**
     * Dashboard hub: links to main modules (weather, map, blog, shop, etc.).
     */
    public function __invoke(Request $request)
    {
        return Inertia::render('Dashboard');
    }
}
