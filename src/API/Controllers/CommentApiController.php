<?php

namespace Nepal360\FilamentCmsPro\API\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Nepal360\FilamentCmsPro\Models\Post;
use Nepal360\FilamentCmsPro\Models\Comment;
use Nepal360\FilamentCmsPro\Events\CommentSubmittedEvent;
use Illuminate\Http\JsonResponse;

class CommentApiController extends Controller
{
    /**
     * Display post comments.
     */
    public function index(Post $post, Request $request): JsonResponse
    {
        $comments = $post->comments()
            ->where('status', 'approved')
            ->whereNull('parent_id')
            ->with('replies')
            ->orderBy('created_at', 'desc')
            ->paginate($request->query('per_page', 20));

        return response()->json($comments);
    }

    /**
     * Store a newly created comment.
     */
    public function store(Post $post, Request $request): JsonResponse
    {
        $rules = [
            'content' => 'required|string|min:5',
            'parent_id' => 'nullable|exists:comments,id',
        ];

        // Guest comment validation rules
        if (!auth()->check()) {
            $rules['author_name'] = 'required|string|max:255';
            $rules['author_email'] = 'required|email|max:255';
        }

        $request->validate($rules);

        $comment = new Comment([
            'post_id' => $post->id,
            'user_id' => auth()->id(),
            'parent_id' => $request->input('parent_id'),
            'author_name' => auth()->check() ? auth()->user()->name : $request->input('author_name'),
            'author_email' => auth()->check() ? auth()->user()->email : $request->input('author_email'),
            'content' => $request->input('content'),
            'status' => 'pending', // Pending by default until moderated/spam scanned
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        $comment->save();

        // Dispatch comment submission event (triggers spam scans and notifications)
        event(new CommentSubmittedEvent($comment));

        return response()->json([
            'message' => 'Comment submitted successfully.',
            'data' => [
                'id' => $comment->id,
                'post_id' => $comment->post_id,
                'parent_id' => $comment->parent_id,
                'author_name' => $comment->author_name,
                'content' => $comment->content,
                'status' => $comment->status,
                'created_at' => $comment->created_at,
            ]
        ], 210); // Returning standard 201 response or similar
    }
}
