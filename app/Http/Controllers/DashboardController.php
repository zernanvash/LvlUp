<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project; // make sure this is here

class DashboardController extends Controller
{
    public function dashboard()
    {
        $projects = Project::where('is_published', true)->get();
        return view('dashboard', compact('projects'));
    }
}
