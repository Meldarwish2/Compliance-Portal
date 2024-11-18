<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        $projects = []; // Fetch projects data
        $chartData = []; // Prepare data for the pie chart

        return view('admin.dashboard', compact('projects', 'chartData'));
    }
}
