<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Models\Post;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PostController extends Controller
{
    public function index()
    {

        return Inertia::render('posts/Index', [
            'posts' => Post::with('author:id,first_name,last_name')->paginate(30),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'author_id' => 'required|exists:authors,id',
            'published' => 'boolean',
        ]);

        $data['content'] = $data['description'];

        Post::create($data);

        return redirect()->route('posts.index');
    }

    public function show(Post $post)
    {
        if (empty($post->description) && ! empty($post->content)) {
            $post->description = $post->content;
        }

        return Inertia::render('posts/View', [
            'post' => $post->loadMissing('author', 'comments'),
        ]);
    }

    public function edit(Post $post)
    {
        if (empty($post->description) && ! empty($post->content)) {
            $post->description = $post->content;
        }

        return Inertia::render('posts/Edit', [
            'post' => $post,
            'authors' => Author::all()->mapWithKeys(fn ($author) => [$author->id => $author->first_name.' '.$author->last_name])->toArray(),
        ]);
    }

    public function update(Request $request, Post $post)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'author_id' => 'required|exists:authors,id',
            'published' => 'boolean',
        ]);

        $data['content'] = $data['description'];

        $post->update($data);

        return redirect()->route('posts.index')->with('success', 'Post updated successfully.');
    }

    public function destroy(Post $post)
    {
        $post->delete();

        return redirect()->back()->with('success', 'Postitus kustutatud.');
    }
}
