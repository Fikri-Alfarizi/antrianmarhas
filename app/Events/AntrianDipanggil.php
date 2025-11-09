<?php

namespace App\Events;

use App\Models\Antrian;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AntrianDipanggil implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $antrian;
    public $loket;
    public $layanan;

    /**
     * Create a new event instance.
     */
    public function __construct(Antrian $antrian, $loket = null, $layanan = null)
    {
        $this->antrian = $antrian;
        $this->loket = $loket;
        $this->layanan = $layanan;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('antrian'),
        ];
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'kode_antrian' => $this->antrian->kode_antrian,
            'nama_layanan' => $this->layanan ?? 'Ruangan',
            'nama_loket' => $this->loket ?? 'Loket',
            'waktu_panggil' => $this->antrian->waktu_panggil?->toIso8601String(),
            'antrian_id' => $this->antrian->id,
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'antrian.dipanggil';
    }
}
