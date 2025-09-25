<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewMessageEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    public function __construct(public Message $messageModel)
    {
        $this->message = $messageModel->load('sender');
    }

    public function broadcastOn(): Channel
    {
        return new PrivateChannel('conversation.' . $this->messageModel->conversation_id);
    }

    public function broadcastWith(): array
    {
        return [
            'message' => [
                'id' => $this->messageModel->id,
                'conversation_id' => $this->messageModel->conversation_id,
                'sender_id' => $this->messageModel->sender_id,
                'message' => $this->messageModel->message,
                'is_read' => $this->messageModel->is_read,
                'created_at' => $this->messageModel->created_at->toISOString(),
                'sender' => [
                    'id' => $this->messageModel->sender->id,
                    'name' => $this->messageModel->sender->name,
                    'role' => $this->messageModel->sender->role,
                    'profile_photo' => $this->messageModel->sender->profile_photo,
                ]
            ]
        ];
    }

    public function broadcastAs(): string
    {
        return 'new.message';
    }
}