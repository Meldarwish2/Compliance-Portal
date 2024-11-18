<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Statement;
use Illuminate\Http\Request;

class StatementController extends Controller
{
    
     // Store new statement
     public function store(Request $request, Project $project)
     {
        $validated= $request->validate([
             'content' => 'required|string|max:255',
             'project_id' => 'required|exists:projects,id',
         ]);
       
         if (!$request->has('content') || !$request->has('project_id')) {
            return response()->json([
                'success' => false,
                'message' => 'Missing content or project ID.',
            ], 400);
        }
    
         // Create the statement
         try {
            $statement = Statement::create([
                'project_id' => $validated['project_id'],
                'content' => $validated['content'],
                'creator_role' => auth()->user()->getRoleNames()->first(),
                'created_by' => auth()->id(),
            ]);
    
            // Return success response
            return response()->json([
                'success' => true,
                'message' => 'Statement added successfully.',
                'statement' => $statement,
            ]);
    
        } catch (\Exception $e) {
            // If there's an error, catch it and return a response
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating the statement: ' . $e->getMessage(),
            ], 500);
        }
        //  return redirect()->route('projects.index')->with('success', 'Statement added successfully.');
     }
     

 
     // Show all statements for a project
     public function show(Project $project)
     {
         $statements = $project->statements;
         return view('statements.index', compact('statements'));
     }
 
     // Delete statement (admin or auditor)
     public function destroy(Statement $statement)
     {
         $statement->delete();
         return back()->with('success', 'Statement deleted successfully.');
     }
}
