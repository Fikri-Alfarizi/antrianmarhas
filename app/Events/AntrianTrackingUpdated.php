<?php

namespace App\Events;

use App\Models\Antrian;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithBroadcasting;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AntrianTrackingUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithBroadcasting, SerializesModels;

    public $antrian;
    public $action; // 'called', 'served', 'finished'
    public $admin;

    public function __construct(Antrian $antrian, $action, $admin = null)
    {
        $this->antrian = $antrian->load('loket', 'layanan');
        $this->action = $action;
        $this->admin = $admin;
    }

    public function broadcastOn()
    {
        return new Channel('antrian-tracking');
    }

    public function broadcastAs()
    {
        return 'antrian.tracking.updated';
    }

    public function broadcastWith()
    {
        return [
            'antrian_id' => $this->antrian->id,
            'kode_antrian' => $this->antrian->kode_antrian,
            'loket_id' => $this->antrian->loket_id,
            'loket_nama' => $this->antrian->loket->nama_loket ?? null,
            'action' => $this->action,
            'status' => $this->antrian->status,
            'timestamp' => now()->toIso8601String(),
            'admin_name' => $this->admin?->name ?? 'System',
        ];
    }
}
