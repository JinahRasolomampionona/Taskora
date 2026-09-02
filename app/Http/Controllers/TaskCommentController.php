<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskCommentRequest;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class TaskCommentController extends Controller
{
    public function store(StoreTaskCommentRequest $request, Task $task): JsonResponse|RedirectResponse
    {
        $comment = $task->comments()->create([
            'user_id' => $request->user()->id,
            'body' => $request->validated('body'),
            'is_internal' => $request->isInternal(),
        ]);

        $comment->load('user:id,name,role');

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Commentaire ajoute.',
                'data' => [
                    'id' => $comment->id,
                    'body' => $comment->body,
                    'is_internal' => $comment->is_internal,
                    'created_at' => $comment->created_at->format('d/m/Y H:i'),
                    'author' => [
                        'name' => $comment->user->name,
                        'initials' => $comment->user->initials,
                        'color' => $comment->user->avatar_color,
                    ],
                ],
            ], 201);
        }

        return back()->with('success', 'Commentaire ajoute.');
    }
}
