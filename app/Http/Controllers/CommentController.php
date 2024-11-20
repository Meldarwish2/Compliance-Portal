<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Statement;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request, Statement $statement)
    {
        $request->validate(['content' => 'required']);
        Comment::create([
            'statement_id' => $statement->id,
            'content' => $request->content,
            'user_id' => auth()->id(),
            'role' => auth()->user()->getRoleNames()->first(),
        ]);
        return back()->with('success', 'Comment added successfully.');
    }

    public function destroy(Comment $comment)
    {
        $comment->delete();
        return back()->with('success', 'Comment deleted successfully.');
    }
}
