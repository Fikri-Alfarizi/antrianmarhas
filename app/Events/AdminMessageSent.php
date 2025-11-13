<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Loket;

class AdminMessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $loket;
    public $pesan;
    public $admin_name;

    /**
     * Buat instance event baru.
     */
    public function __construct(Loket $loket, string $pesan, string $admin_name)
    {
        $this->loket = $loket;
        $this->pesan = $pesan;
        $this->admin_name = $admin_name;
    }

    /**
     * Tentukan channel tempat event ini akan disiarkan.
     */
    public function broadcastOn(): array
    {
        // Channel khusus untuk loket ini
        return [
            new PrivateChannel('loket-' . $this->loket->id),
        ];
    }

    /**
     * Nama event yang akan disiarkan.
     */
    public function broadcastAs(): string
    {
        return 'admin.message.sent';
    }

    /**
     * Data yang akan disiarkan.
     */
    public function broadcastWith(): array
    {
        return [
            'loket_id' => $this->loket->id,
            'loket_nama' => $this->loket->nama_loket,
            'pesan' => $this->pesan,
            'admin_name' => $this->admin_name,
            'waktu' => now()->format('H:i:s'),
        ];
    }
}