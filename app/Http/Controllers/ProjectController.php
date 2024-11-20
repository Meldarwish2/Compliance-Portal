<?php
namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    // public function index()
    // {
    //     $projects = Project::with(['users', 'statements', 'evidences'])->get();
    //     return view('projects.index', compact('projects'));
    // }

    // public function create()
    // {
    //     return view('projects.create');
    // }

    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'name' => 'required',
    //         'description' => 'required',
    //     ]);

    //     Project::create($request->all());

    //     return redirect()->route('projects.index')->with('success', 'Project created successfully.');
    // }

    // public function show(Project $project)
    // {
    //     $project->load('users', 'statements', 'evidences');

    //     $users = User::get();
    //     return view('projects.show', compact(['project','users']));
    // }

    // public function edit(Project $project)
    // {
    //     return view('projects.edit', compact('project'));
    // }

    // public function update(Request $request, Project $project)
    // {
    //     $request->validate([
    //         'name' => 'required',
    //         'description' => 'required',
    //     ]);

    //     $project->update($request->all());

    //     return redirect()->route('projects.index')->with('success', 'Project updated successfully.');
    // }

    // public function destroy(Project $project)
    // {
    //     $project->delete();

    //     return redirect()->route('projects.index')->with('success', 'Project deleted successfully.');
    // }

    // public function assign(Request $request, Project $project)
    // {
    //     $request->validate([
    //         'user_id' => 'required|exists:users,id',
    //     ]);

    //     $project->users()->attach($request->user_id);

    //     return redirect()->route('projects.show', $project)->with('success', 'User assigned successfully.');
    // }
    // Show all projects assigned to the authenticated client
    public function index()
    {
        $users = User::all();
        if (Auth::user()->hasRole('admin')) {
            $projects = Project::with(['users', 'statements', 'evidences'])->get();
        }
        else {
            $projects = Auth::user()->projects; // Only show assigned projects for the client role
        }
        return view('projects.index', compact(['projects','users']));
    }

    // Show project details along with users, statements, and evidences
    public function show(Project $project)
    {
        $users = User::all();  // To assign project to a user, admin will need to select from all users
        return view('projects.show', compact('project', 'users'));
    }

    // Admin assigns project to a user (auditor/client)
    public function assign(Request $request, Project $project)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        // Assign project to the selected user
        $project->users()->attach($request->user_id);

        return back()->with('success', 'Project assigned successfully.');
    }

    // Admin revokes access for a user
    public function revokeAccess(Project $project, User $user)
    {
        $project->users()->detach($user);
        return back()->with('success', 'Access revoked.');
    }

    // Store new project
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        $project = Project::create($request->only('name', 'description'));

        return redirect()->route('projects.index')->with('success', 'Project created successfully.');
    }

    // Show form to create a new project
    public function create()
    {
        return view('projects.create');
    }

    // Edit an existing project
    public function edit(Project $project)
    {
        return view('projects.edit', compact('project'));
    }

    // Update project details
    public function update(Request $request, Project $project)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        $project->update($request->only('name', 'description'));

        return redirect()->route('projects.index')->with('success', 'Project updated successfully.');
    }

    // Delete a project
    public function destroy(Project $project)
    {
        $project->delete();
        return redirect()->route('projects.index')->with('success', 'Project deleted successfully.');
    }
}
