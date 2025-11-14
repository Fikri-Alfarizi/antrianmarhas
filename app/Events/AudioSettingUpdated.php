<?php

namespace App\Events;

use App\Models\AudioSetting;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AudioSettingUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $audioSetting;

    /**
     * Create a new event instance.
     */
    public function __construct(AudioSetting $audioSetting)
    {
        $this->audioSetting = $audioSetting;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('audio-settings'),
        ];
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'id' => $this->audioSetting->id,
            'tipe' => $this->audioSetting->tipe,
            'bahasa' => $this->audioSetting->bahasa,
            'volume' => $this->audioSetting->volume,
            'aktif' => $this->audioSetting->aktif,
            'format_pesan' => $this->audioSetting->format_pesan,
            'voice_url' => $this->audioSetting->voice_url,
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'audio.setting.updated';
    }
}
