<?php

namespace Modules\LiveChat\App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\LiveChat\App\Models\Message;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('chat.'.$this->message->conversation_id);
    }

    public function broadcastWith()
    {
        return [
            'message' => $this->message->load('sender'),
        ];
    }

    public function broadcastAs()
    {
        return 'message.sent';
    }
}
