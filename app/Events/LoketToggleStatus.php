<?php

namespace App\Events;

use App\Models\Loket;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithBroadcasting;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LoketToggleStatus implements ShouldBroadcast
{
    use Dispatchable, InteractsWithBroadcasting, SerializesModels;

    public $loket;
    public $newStatus;

    public function __construct(Loket $loket, $newStatus)
    {
        $this->loket = $loket;
        $this->newStatus = $newStatus;
    }

    public function broadcastOn()
    {
        return new Channel('antrian-channel');
    }

    public function broadcastAs()
    {
        return 'loket.toggle.status';
    }

    public function broadcastWith()
    {
        return [
            'loket_id' => $this->loket->id,
            'status' => $this->newStatus,
            'nama_loket' => $this->loket->nama_loket,
            'timestamp' => now()->toIso8601String(),
        ];
    }
}
