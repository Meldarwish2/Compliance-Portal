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
      $comment =  Comment::create([
            'statement_id' => $statement->id,
            'content' => $request->content,
            'user_id' => auth()->id(),
            'role' => auth()->user()->getRoleNames()->first(),
        ]);
        return response()->json([
            'success' => true,
            'message' => 'Comment added successfully',
            'comment' => $comment,
        ]);
    }

    public function destroy(Comment $comment)
    {
        $comment->delete();
        return back()->with('success', 'Comment deleted successfully.');
    }
}
