<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithBroadcasting;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AdminMessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithBroadcasting, SerializesModels;

    public $message;
    public $fromAdmin;
    public $toUser;
    public $messageId;

    public function __construct($message, $fromAdmin, $toUser, $messageId)
    {
        $this->message = $message;
        $this->fromAdmin = $fromAdmin;
        $this->toUser = $toUser;
        $this->messageId = $messageId;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('admin-messages.' . $this->toUser->id);
    }

    public function broadcastAs()
    {
        return 'admin.message.sent';
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->messageId,
            'message' => $this->message,
            'from_admin' => $this->fromAdmin->name,
            'from_admin_id' => $this->fromAdmin->id,
            'timestamp' => now()->toIso8601String(),
        ];
    }
}
