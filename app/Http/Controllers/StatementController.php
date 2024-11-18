<?php

namespace App\Http\Controllers;

use App\Models\Statement;
use Illuminate\Http\Request;

class StatementController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'content' => 'required',
            'project_id' => 'required|exists:projects,id',
        ]);

        $statement = Statement::create([
            'content' => $request->content,
            'project_id' => $request->project_id,
        ]);

        return redirect()->route('projects.show', $statement->project_id)->with('success', 'Statement created successfully.');
    }
}
