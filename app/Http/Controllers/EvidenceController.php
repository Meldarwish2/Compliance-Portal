<?php
namespace App\Http\Controllers;

use App\Models\Evidence;
use App\Models\Project;
use App\Models\Statement;
use Illuminate\Http\Request;

class EvidenceController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'file_name' => 'required',
            'file_path' => 'required',
            'project_id' => 'required|exists:projects,id',
        ]);
        $evidence = Evidence::create([
            'file_name' => $request->file_name ??'',
            'file_path' => $request->file_path,
            'project_id' => $request->project_id,
            'uploaded_by' => auth()->user()->id,
        ]);

        return redirect()->route('projects.show', $evidence->project_id)->with('success', 'Evidence uploaded successfully.');
    }

    public function approve(Evidence $evidence)
    {
        $evidence->update(['status' => 'approved']);
        
        // approve statement also 
        $evidence->statement->update(['status' => 'approved']);
        return redirect()->route('projects.show', $evidence->project_id)->with('success', 'Evidence approved.');
    }

    public function reject(Evidence $evidence)
    {
        $evidence->update(['status' => 'rejected']);
        $evidence->statement->update(['status' => 'rejected']);

        return redirect()->route('projects.show', $evidence->project_id)->with('success', 'Evidence rejected.');
    }
    // Upload evidence for a specific project
    public function upload(Request $request, Statement $statement)
    {
        $request->validate([
            'evidence' => 'required|file|mimes:pdf,docx,xlsx,jpeg,png,jpg,eml|max:10240',
        ]);
        $file = $request->file('evidence');
        $filePath = $file->storeAs('evidences', $file->getClientOriginalName(), 'public');

        // Create Evidence record
        $evidence = new Evidence();
        $evidence->statement_id = $statement->id;
        $evidence->file_name = $file->getClientOriginalName();
        $evidence->uploaded_by = auth()->user()->id;
        $evidence->file_path = $filePath;
        $evidence->save();

        return response()->json(['success' => true, 'message' => 'Evidence uploaded successfully.']);

    }

    // Allow auditors to download evidence (without viewing)
    public function download(Evidence $evidence)
    {
        return response()->download(storage_path("app/public/{$evidence->file_path}"));
    }

    // Display all evidence uploaded for a project (for admin)
    public function index(Project $project)
    {
        $evidences = $project->evidences;
        return view('evidences.index', compact('evidences'));
    }
}
