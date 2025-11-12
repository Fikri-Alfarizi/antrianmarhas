<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Loket;

class LoketStatusUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $loket;

    /**
     * Buat instance event baru.
     */
    public function __construct(Loket $loket)
    {
        $this->loket = $loket;
    }

    /**
     * Tentukan channel tempat event ini akan disiarkan.
     */
    public function broadcastOn(): array
    {
        // Kita gunakan channel yang sama dengan antrian
        return [
            new Channel('antrian-channel'),
        ];
    }

    /**
     * Nama event yang akan disiarkan.
     */
    public function broadcastAs(): string
    {
        return 'loket.status.updated';
    }

    /**
     * Data yang akan disiarkan.
     */
    public function broadcastWith(): array
    {
        return [
            'loket_id' => $this->loket->id,
            'status' => $this->loket->status,
            'nama_loket' => $this->loket->nama_loket,
        ];
    }
}