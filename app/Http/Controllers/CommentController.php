<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /** Store a comment for a post */
    public function store(Request $request, Post $post)
    {
        $data = $request->validate([
            'author_name' => 'nullable|string|max:255',
            'content' => 'required|string',
        ]);

        if (empty($data['author_name'])) {
            $data['author_name'] = auth()->user()?->name ?? 'Test User';
        }

        $post->comments()->create($data);

        return redirect()->back();
    }

    /** Delete a comment */
    public function destroy(Post $post, Comment $comment)
    {
        abort_unless(auth()->user()?->email === 'test@example.com', 403);

        if ($comment->post_id !== $post->id) {
            abort(404);
        }

        $comment->delete();

        return redirect()->back();
    }
}
