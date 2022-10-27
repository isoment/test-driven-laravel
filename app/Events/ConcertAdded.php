<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\Concert;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ConcertAdded
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Concert $concert;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Concert $concert)
    {
        $this->concert = $concert;
    }
}
