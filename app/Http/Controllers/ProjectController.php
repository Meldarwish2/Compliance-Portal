<?php
namespace App\Http\Controllers;

use App\Imports\StatementsImport;
use App\Models\Evidence;
use App\Models\Project;
use App\Models\Statement;
use App\Models\User;
use App\Notifications\AuditorActionNotification;
use App\Notifications\ProjectAssignmentNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use ZipArchive;

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
        } else {
            $projects = Auth::user()->projects; // Only show assigned projects for the client role
        }
        return view('projects.index', compact(['projects', 'users']));
    }

    // Show project details along with users, statements, and evidences
    public function show(Project $project)
    {
        $users = User::all(); // For admin to assign project to a user
        $project->load('statements.comments', 'statements.evidences');

        // Initialize data structures for different project types
        $data = [];

        switch ($project->type) {
            case 'rating':
                $data['ratings'] = [0, 0, 0, 0, 0]; // Ratings 1 to 5
                $data['statuses'] = [
                    'rejected' => 0,
                    'pending' => 0,
                    'assigned_to_qa' => 0,
                ];

                foreach ($project->statements as $statement) {
                    if ($statement->rating >= 1 && $statement->rating <= 5) {
                        $data['ratings'][$statement->rating - 1]++;
                    }
                    switch ($statement->status) {
                        case 'rejected':
                            $data['statuses']['rejected']++;
                            break;
                        case 'pending':
                            $data['statuses']['pending']++;
                            break;
                        case 'assigned_to_qa':
                            $data['statuses']['assigned_to_qa']++;
                            break;
                    }
                }
                break;

            case 'accept_reject':
                $data['statuses'] = [
                    'approved' => 0,
                    'rejected' => 0,
                    'pending' => 0,
                ];

                foreach ($project->statements as $statement) {
                    switch ($statement->status) {
                        case 'approved':
                            $data['statuses']['approved']++;
                            break;
                        case 'pending':
                            $data['statuses']['pending']++;
                            break;
                        case 'rejected':
                            $data['statuses']['rejected']++;
                            break;
                    }
                }
                break;

            case 'compliance':
                $data['statuses'] = [
                    'compliant' => 0,
                    'partially_compliant' => 0,
                    'rejected' => 0,
                    'pending' => 0,
                ];

                foreach ($project->statements as $statement) {
                    switch ($statement->status) {
                        case 'compliant':
                            $data['statuses']['compliant']++;
                            break;
                        case 'partially_compliant':
                            $data['statuses']['partially_compliant']++;
                            break;
                        case 'pending':
                            $data['statuses']['pending']++; 
                            break;    
                        case 'rejected':
                            $data['statuses']['rejected']++;
                            break;
                    }
                }
                break;
        }

        return view('projects.show', compact('project', 'users', 'data'));
    }


    // Admin assigns project to a user (auditor/client)
    public function assign(Request $request, Project $project)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        // Assign project to the selected user
        $project->users()->attach($request->user_id);
        $user = User::find($request->user_id);
        $user->notify(new ProjectAssignmentNotification($project, 'assigned'));

        return back()->with('success', 'Project assigned successfully.');
    }

    // Admin revokes access for a user
    public function revokeAccess(Project $project, Request $request)
    {

        $user = User::find($request->user_id);
        $user->notify(new ProjectAssignmentNotification($project, 'revoked'));
        $project->users()->detach($user);
        return back()->with('success', 'Access revoked.');
    } 
       public function assignUsers(Project $project)
    {
        $users = User::all();
        return view('projects.assign', compact('project', 'users'));
    }

    // Store new project
    public function store(Request $request)
    {
        $validated =  $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'client_name' => 'nullable|string',
            'csv_file' => 'nullable|file|mimes:csv,txt,xlsx,xls|max:2048',
            'parent_project_id' => [
                'nullable',
                'exists:projects,id',
                function ($attribute,
                    $value,
                    $fail
                ) use ($request) {
                    if ($request->id === $value) {
                        $fail('A project cannot be its own parent.');
                    }
                },
            ],

        ]);
        if ($validated['parent_project_id'] != null) {
            $parent = Project::find($validated['parent_project_id']);
            $project = Project::create([
                'name' => $request->name,
                'description' => $parent->description,
                'parent_project_id' => $parent->id,
                'type' => $parent->type,
            ]);
            // Copy statements from parent to new project
            foreach ($parent->statements as $statement) {
                $project->statements()->create($statement->toArray());
            }
        }
        else
        {

            $project = Project::create($request->only('name', 'description','parent_project_id'));
        }

        if ($request->hasFile('csv_file')) {

            Excel::import(new StatementsImport($project->id), $request->file('csv_file'));
        }
        $admin = User::role('admin')->first();
        $project->users()->attach($admin->id);
        return redirect()->route('projects.index')->with('success', 'Project created successfully.');
    }

    // Show form to create a new project
    public function create()
    {
        $projects = Project::all();
        return view('projects.create', compact('projects'));
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
    public function destroy(Request $request, Project $project)
    {
        $deleteOption = $request->input('delete_option');
        $zipPassword = $request->input('zip_password');

        if ($deleteOption === 'delete') {
            // Delete all related files
            $project->statements->each(function ($statement) {
                $statement->evidences->each(function ($evidence) {
                    Storage::delete($evidence->file_path);
                });
            });
        } elseif ($deleteOption === 'archive') {
            // Create a password-protected ZIP archive
            $zip = new ZipArchive();
            $zipFileName = 'archive_' . $project->id .'_'.$project->name . '.zip';
            $zipFilePath = storage_path('app/public/' . $zipFileName);

            if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
                $project->statements->each(function ($statement) use ($zip) {
                    $statement->evidences->each(function ($evidence) use ($zip) {
                        $filePath = storage_path('app/public/' . $evidence->file_path);
                        if (file_exists($filePath)) {
                            $zip->addFile($filePath, basename($evidence->file_path));
                        }
                    });
                });
            
                // Set a password for the ZIP archive
                if ($zipPassword) {
                    $zip->setPassword($zipPassword);
            
                    // Encrypt each file in the ZIP archive
                    for ($i = 0; $i < $zip->numFiles; $i++) {
                        $zip->setEncryptionIndex($i, ZipArchive::EM_AES_256, $zipPassword);
                    }
                }
            
                $zip->close();
            
                // Delete the original files after creating the ZIP archive
                $project->statements->each(function ($statement) {
                    $statement->evidences->each(function ($evidence) {
                        Storage::delete($evidence->file_path);
                    });
                });
            } else {
                return redirect()->back()->with('error', 'Failed to create ZIP archive.');
            }
            
        }

        // Delete the project
        $project->delete();

        return redirect()->route('projects.index')->with('success', 'Project deleted successfully.');
    }

    public function UploadStatementCsv(Request $request, Project $project)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt,xlsx,xls|max:2048',
        ]);

        if ($request->hasFile('csv_file')) {
            Excel::import(new StatementsImport($project->id), $request->file('csv_file'));
        }
        return redirect()->route('projects.show', $project)->with('success', 'Statements uploaded successfully.');
    }

    // New methods for rating and compliance
    public function rateEvidence(Request $request, $evidenceId)
    {
        $request->validate([
            'rating' => 'required|integer|between:1,5',
        ]);
        $evidence = Evidence::findOrFail($evidenceId);
        $evidence->rating = $request->rating;
        $evidence->statement->rating = $request->rating;
        $evidence->status = Evidence::STATUS_APPROVED;
        $evidence->statement->status = Statement::STATUS_APPROVED;
        $evidence->save();
        $evidence->statement->save();
        $user = User::find($evidence->uploaded_by);
        $user->notify(new AuditorActionNotification( Evidence::STATUS_APPROVED,  $evidence->statement));    
        return response()->json(['success' => true, 'message' => 'Evidence rated successfully.']);
    
    }

    public function complianceEvidence(Request $request, $evidenceId)
    {
        $request->validate([
            'compliance' => 'required|in:compliant,partially_compliant,rejected',
        ]);

        $evidence = Evidence::findOrFail($evidenceId);
        $evidence->compliance = $request->compliance;
        if (in_array($request->compliance, ['partially_compliant', 'compliant'])) {
        
            $evidence->status = Evidence::STATUS_APPROVED;
            $evidence->statement->status = Statement::STATUS_APPROVED;
            $user = User::find($evidence->uploaded_by);
            $user->notify(new AuditorActionNotification( Evidence::STATUS_APPROVED,  $evidence->statement));   
        }
        else
        {
            $evidence->status = Evidence::STATUS_REJECTED;
            $evidence->statement->status = Statement::STATUS_REJECTED;
            $user = User::find($evidence->uploaded_by);
            $user->notify(new AuditorActionNotification( Evidence::STATUS_REJECTED,  $evidence->statement));   
        }
        $evidence->save();
        $evidence->statement->save();
        // return response()->json(['success' => true, 'message' => 'Compliance status updated successfully.']);
        return redirect()->back()->with('success', 'Compliance status updated successfully.');
        
    }
}