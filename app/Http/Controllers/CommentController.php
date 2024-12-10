<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Statement;
use App\Notifications\CommentAddedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
    public function store(Request $request, Statement $statement)
    {
        $commentContent = $request->comment;

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

        $comment = Comment::create([
            'statement_id' => $statement->id,
            'content' => $commentContent,
            'user_id' => auth()->id(),
            'role' => auth()->user()->getRoleNames()->first(),
        ]);

        // Notify users associated with the project
        $project = $statement->project;

        // Determine user roles from the project
        $users = $project->users;
        foreach ($users as $user) {
            if ($comment->role === 'auditor' && $user->hasRole('client')) {
                // Notify client if auditor adds a comment
                $user->notify(new CommentAddedNotification($comment));
            } elseif ($comment->role === 'client' && $user->hasRole('auditor')) {
                // Notify auditor if client adds a comment
                $user->notify(new CommentAddedNotification($comment));
            }
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Comment added successfully',
                'comment' => $comment,
            ]);
        }

        return back()->with('success', 'Comment created successfully.');
    }

    public function destroy(Comment $comment)
    {
        $comment->delete();
        return back()->with('success', 'Comment deleted successfully.');
    }
}
