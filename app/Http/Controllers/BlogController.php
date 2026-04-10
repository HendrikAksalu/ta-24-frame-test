<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Inertia\Inertia;
use Inertia\Response;

class BlogController extends Controller
{
    public function index(): Response
    {
        $posts = Post::query()
            ->with('author:id,first_name,last_name')
            ->where('published', true)
            ->latest()
            ->paginate(12);

        return Inertia::render('blog/Index', [
            'posts' => $posts,
        ]);
    }

    public function show(Post $post): Response
    {
        abort_unless($post->published, 404);

        return Inertia::render('blog/View', [
            'post' => $post->loadMissing('author', 'comments'),
        ]);
    }
}
