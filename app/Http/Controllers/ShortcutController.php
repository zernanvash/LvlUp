<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;

class ShortcutController extends Controller
{
    public function newProject(): RedirectResponse
    {
        return redirect()->route('projects.create');
    }
}
