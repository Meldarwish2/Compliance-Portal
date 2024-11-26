<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Statement;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        if ($user->hasRole('admin')) {
            // Admin can see all projects and clients
            $projects = Project::withCount([
                'statements as completed_statements_count' => function ($query) {
                    $query->where('status', Statement::STATUS_APPROVED);
                },
                'statements as pending_statements_count' => function ($query) {
                    $query->where('status', Statement::STATUS_PENDING);
                },
                'statements as rejected_statements_count' => function ($query) {
                    $query->where('status', Statement::STATUS_REJECTED);
                }
            ])->get();

        } else {
            // Client or Auditor can see only their assigned projects
            $projects = $user->projects()->withCount([
                'statements as completed_statements_count' => function ($query) {
                    $query->where('status', Statement::STATUS_APPROVED);
                },
                'statements as pending_statements_count' => function ($query) {
                    $query->where('status', Statement::STATUS_PENDING);
                },
                'statements as rejected_statements_count' => function ($query) {
                    $query->where('status', Statement::STATUS_REJECTED);
                }
            ])->get();

        }

        return view('admin.dashboard', compact('projects'));
    }
}