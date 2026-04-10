<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CommentController extends Controller
{
    /** Store a comment for a post */
    public function store(Request $request, Post $post)
    {
        if (! $post->published && ! $request->user()) {
            abort(404);
        }

        $data = $request->validate([
            'author_name' => [
                Rule::requiredIf(! $request->user()),
                'nullable',
                'string',
                'max:255',
            ],
            'content' => 'required|string',
        ]);

        if ($request->user()) {
            $data['author_name'] = trim((string) ($data['author_name'] ?? '')) !== ''
                ? $data['author_name']
                : $request->user()->name;
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
