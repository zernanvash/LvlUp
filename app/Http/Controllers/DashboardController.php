<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project; // make sure this is here

class DashboardController extends Controller
{
    public function dashboard()
    {
        $projects = Project::all();
        return view('dashboard', compact('projects'));
    }
}
