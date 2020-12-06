<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{

    public function __construct()
    {
        $this->middleware(['auth'])->only(['store', 'destroy', 'duplicate']);
    }

    public function randomPost(Request $request)
    {
        $post = new Post;
        return back();
    }

    public function duplicate(Post $post, Request $request)
    {
        $new_post = $post->replicate();
        $new_post->user_id = $request->user()->id;
        $new_post->save();
        return back();
    }

    public function index()
    {
        
        $posts = Post::orderBy('created_at', 'desc')->with(['user', 'likes'])->paginate(20);
        return view('posts.index', [
            "posts" => $posts
        ]);
    }

    public function show(Post $post)
    {
        return view('posts.show', [
            'post' => $post 
        ]);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'body' => 'required'
        ]);

        $request->user()->posts()->create($request->only('body'));

        return back();
    }

    public function destroy(Post $post)
    {

        $this->authorize('delete', $post);
        $post->delete();

        return back();
    }
}
