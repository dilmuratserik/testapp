<?php

namespace App\Events;

use App\Models\Post;
use App\Models\User;
use http\Env\Request;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;

class PostBlocked
{
    use Dispatchable, SerializesModels;

    public $post;
    public $moderator;

    public function __construct(Post $post, User $moderator)
    {
        $this->post = $post;
        $this->moderator = $moderator;
    }
    public function block(Request $request, $id)
    {
        $moderator = Auth::user();
        if ($moderator->role != 'moderator') {
            return response()->json(['error' => 'User is not a moderator'], 403);
        }

        $post = Post::findOrFail($id);
        $post->status = 'blocked';
        $post->block_reason = $request->input('reason');
        $post->save();

        event(new PostBlocked($post, $moderator));  // здесь вызывается событие

        return response()->json($post);
    }
}
