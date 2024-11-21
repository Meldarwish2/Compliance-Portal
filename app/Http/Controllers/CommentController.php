<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Statement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
    public function store(Request $request, Statement $statement)
    {
        $commentContent = $request->getContent();

        // Define custom validation rules
        $validator = Validator::make(['content' => $commentContent], [
            'content' => ['required', 'string', 'min:3', 'max:500'],
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 400);
        }
      $comment =  Comment::create([
            'statement_id' => $statement->id,
            'content' => $commentContent,
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
