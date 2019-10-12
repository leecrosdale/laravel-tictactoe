<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class Vote
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $col;
    public $row;
    public $team;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($col, $row, $team)
    {
        $this->col = $col;
        $this->row = $row;
        $this->team = $team;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('game');
    }
}
