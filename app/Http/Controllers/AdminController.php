<?php

namespace App\Http\Controllers;

use App\DataTables\ActevitiesDataTable;
use App\Models\Audit;
use App\Models\Project;
use App\Models\Statement;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use OwenIt\Auditing\Models\Audit as ModelsAudit;
use Yajra\DataTables\Facades\DataTables;

class AdminController extends Controller
{
    public function index()
    {
        $user = Auth::user();
    
        // Check for admin role
        if ($user->hasRole('admin')) {
            // Admin can see all projects and users
            $projectsData['totalProjects'] = Project::whereNotNull('parent_project_id')->count();
            $projectsData['projectsApproved'] = Project::whereNotNull('parent_project_id')->where('status', 'approved')->count();
            $projectsData['projectsPending'] = Project::whereNotNull('parent_project_id')->where('status', 'pending')->count();
            $projectsData['projectsRejected'] = Project::whereNotNull('parent_project_id')->where('status', 'rejected')->count();
            $totalUsers = User::count();
            $pendingActions = Project::whereNotNull('parent_project_id')->where('status', 'pending')->count();

            // Fetch projects with relationships
            $projects = Project::whereNotNull('parent_project_id')->with(['users', 'children', 'parent', 'statements'])->get();

            // Calculate completion percentage for each project
            $projects->each(function ($project) {
                $totalStatements = $project->statements->count();
                $approvedStatements = $project->statements->where('status', 'approved')->count();
                $project->completionPercentage = $totalStatements > 0 ? round(($approvedStatements / $totalStatements) * 100) : 10;
            });

            // Assign unique colors to each project
            $colors = [ '#33FF57', '#3357FF', '#FF33A1', '#A133FF', '#33FFF5', '#F5FF33'];
            $projects->each(function ($project, $index) use ($colors) {
                $project->color = $colors[$index % count($colors)];
            });

            return view('admin.dashboard2', compact('projectsData', 'totalUsers', 'pendingActions', 'projects'));
        } else {
            // Client or Auditor can see only their assigned projects
            $projectsData['totalProjects'] = $user->projects()->count();
            $projectsData['projectsApproved'] = $user->projects()->where('status', 'approved')->count();
            $projectsData['projectsPending'] = $user->projects()->where('status', 'pending')->count();
            $projectsData['projectsRejected'] = $user->projects()->where('status', 'rejected')->count();
            $totalUsers = User::count();
            $pendingActions = $user->projects()->where('status', 'pending')->count();

            // Fetch projects with relationships for the current user
            $projects = Project::with(['users', 'children', 'parent', 'statements'])
                ->whereHas('users', function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                })->get();

            // Calculate completion percentage for each project
            $projects->each(function ($project) {
                $totalStatements = $project->statements->count();
                $approvedStatements = $project->statements->where('status', 'approved')->count();
                $project->completionPercentage = $totalStatements > 0 ? round(($approvedStatements / $totalStatements) * 100) : 0;
            });

            // Assign unique colors to each project
            $colors = ['#FF5733', '#33FF57', '#3357FF', '#FF33A1', '#A133FF', '#33FFF5', '#F5FF33'];
            $projects->each(function ($project, $index) use ($colors) {
                $project->color = $colors[$index % count($colors)];
            });

            return view('admin.dashboard2', compact('projectsData', 'totalUsers', 'pendingActions', 'projects'));
        }
    }



    protected function dataTable($clients)
    {
        $clientFilter = request()->get('clientFilter');
        $projectFilter = request()->get('projectFilter');
        $searchValue = request()->get('search')['value']; // Global search value


        return DataTables::of($clients)
            ->filter(function ($query) use ($clientFilter, $projectFilter, $searchValue) {
                // Apply client filter
                if ($clientFilter) {
                    $query->where('name', 'like', '%' . $clientFilter . '%');
                }

                // Apply project filter
                if ($projectFilter) {
                    $query->whereHas('projects', function ($projectQuery) use ($projectFilter) {
                        $projectQuery->where('name', 'like', '%' . $projectFilter . '%');
                    });
                }
                // Apply global search
                if ($searchValue) {
                    $query->where(function ($subQuery) use ($searchValue) {
                        $subQuery->where('name', 'like', '%' . $searchValue . '%')
                            ->orWhereHas('projects', function ($projectSubQuery) use ($searchValue) {
                                $projectSubQuery->where('name', 'like', '%' . $searchValue . '%');
                            });
                    });
                }
            })
            ->addColumn('projects_count', function ($client) {
                return $client->projects->count();
            })
            ->make(true);
    }


    public function autocompleteClients(Request $request)
    {
        $term = $request->input('term');
        $clients = User::role('client')
            ->where('name', 'like', '%' . $term . '%')
            ->pluck('name')
            ->toArray();

        return response()->json($clients);
    }

    public function autocompleteProjects(Request $request)
    {
        $term = $request->input('term');
        $projects = Project::where('name', 'like', '%' . $term . '%')
            ->pluck('name')
            ->toArray();

        return response()->json($projects);
    }

    public function clientProjects(Request $request)
    {
        $clientName = $request->input('client');

        $client = User::where('name', $clientName)->with('projects')->first();

        if (!$client) {
            return response()->json([], 404);
        }

        $projects = $client->projects->map(function ($project) {
            return [
                'id' => $project->id,
                'name' => $project->name,
            ];
        });

        return response()->json($projects);
    }
}
