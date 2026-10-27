<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Profile screen; updates are handled by the Fortify profile and password endpoints.
 */
class ProfileController extends Controller
{
    public function __invoke(Request $request): View
    {
        return view('admin.profile', ['user' => $request->user()]);
    }
}
