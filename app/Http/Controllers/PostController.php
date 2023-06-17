<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostRequest;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::all()
            ->map(function ($post) {
                return [
                    'id' => $post->id,
                    'title' => $post->title,
                    'published_at' => $post->published_at,
                    'excerpt' => Str::limit($post->body, 100)
                ];
            });

        return response()->json($posts);
    }

    public function show($id)
    {
        $post = Post::findOrFail($id);
        return response()->json($post);
    }
    public function store(PostRequest $request)
    {   try {
        $post = new Post($request->validated());
        $post->user_id = Auth::id();
        $post->save();

        return response()->json($post, 201);
    }
    catch (\Exception $e){
        return response()->json([
            'message' => "Something went really wrong!"
        ],500);
    }
    }

    public function update(PostRequest $request, $id)
    {
        $post = Post::findOrFail($id);
        $post->update($request->validated());
        return response()->json($post);
    }

    public function block(Request $request, $id)
    {
        $user = Auth::user();
        if ($user->role != 'moderator') {
            return response()->json(['error' => 'User is not a moderator'], 403);
        }

        $post = Post::findOrFail($id);
        $post->status = 'blocked';
        $post->block_reason = $request->input('reason');
        $post->save();

        return response()->json($post);
    }
    public function myPosts()
    {
        $user = Auth::user();
        $posts = $user->posts;
        return response()->json($posts);
    }
    public function recentPosts()
    {
        $users = User::withCount('posts')
            ->orderBy('posts_count', 'desc')
            ->get()
            ->map(function ($user) {
                $user->recent_posts = $user->posts()->latest()->limit(3)->get();
                return $user;
            });

        return response()->json($users);
    }


}
