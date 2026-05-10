<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BlogController extends Controller
{
    public function index(Request $request): Response
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

    public function create(Request $request): Response
    {
        $authors = Author::query()
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get(['id', 'first_name', 'last_name'])
            ->mapWithKeys(fn (Author $a) => [$a->id => "{$a->first_name} {$a->last_name}"])
            ->toArray();

        return Inertia::render('blog/Create', [
            'authors' => $authors,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'author_id' => ['required', 'exists:authors,id'],
        ]);

        Post::query()->create([
            'title' => $data['title'],
            'description' => $data['description'],
            'content' => $data['description'],
            'author_id' => (int) $data['author_id'],
            'published' => true,
        ]);

        return redirect()->route('blog.index')->with('success', 'Postitus avaldatud.');
    }

    public function show(Post $post): Response
    {
        abort_unless($post->published, 404);

        return Inertia::render('blog/View', [
            'post' => $post->loadMissing('author', 'comments'),
        ]);
    }

    public function destroy(Request $request, Post $post): RedirectResponse
    {
        abort_unless($request->user(), 403);

        $post->comments()->delete();
        $post->delete();

        return redirect()->route('blog.index')->with('success', 'Postitus kustutatud.');
    }
}
