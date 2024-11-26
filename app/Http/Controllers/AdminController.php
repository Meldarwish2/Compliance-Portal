<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Statement;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

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

            // Retrieve clients as a query builder
            $clients = User::role('client')->with('projects');
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

            // Retrieve the client's own data
            $clients = User::where('id', $user->id)->with('projects');
        }

        if ($request->ajax()) {
            return $this->dataTable($clients);
        }

        return view('admin.dashboard', compact('projects', 'clients'));
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
