<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class SubmissionWasBookmarked implements ShouldBroadcast
{
    use InteractsWithSockets;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     *   Get the channels the event should broadcast on.
     *
     *   @return Channel|array
     */
    public function broadcastOn()
    {
        return ['refresh.store'];
    }
}
