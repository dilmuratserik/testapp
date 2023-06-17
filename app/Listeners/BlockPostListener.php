<?php

namespace App\Listeners;

use App\Events\PostBlocked;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class BlockPostListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(PostBlocked $event)
    {
        Log::info('Post ' . $event->post->id . ' was blocked by moderator ' . $event->moderator->name);
    }
}
