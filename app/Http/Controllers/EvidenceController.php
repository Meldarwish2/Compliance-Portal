<?php
namespace App\Http\Controllers;

use App\Models\Evidence;
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
            'file_name' => $request->file_name,
            'file_path' => $request->file_path,
            'project_id' => $request->project_id,
            'uploaded_by' => auth()->user()->id,
        ]);

        return redirect()->route('projects.show', $evidence->project_id)->with('success', 'Evidence uploaded successfully.');
    }

    public function approve(Evidence $evidence)
    {
        $evidence->update(['status' => 'approved']);

        return redirect()->route('projects.show', $evidence->project_id)->with('success', 'Evidence approved.');
    }

    public function reject(Evidence $evidence)
    {
        $evidence->update(['status' => 'rejected']);

        return redirect()->route('projects.show', $evidence->project_id)->with('success', 'Evidence rejected.');
    }
}
