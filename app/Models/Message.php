<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    protected $fillable = [
        'conversation_id',
        'sender_id',
        'message',
        'is_read',
        'metadata'
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'metadata' => 'array'
    ];

    // العلاقة مع المحادثة
    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    // العلاقة مع المرسل
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    // تحديد إذا كانت الرسالة مرسلة من المستخدم الحالي
    public function getIsFromMeAttribute(): bool
    {
        return $this->sender_id === auth()->id();
    }

    // وضع علامة مقروء على الرسالة
    public function markAsRead(): void
    {
        if (!$this->is_read && !$this->is_from_me) {
            $this->update(['is_read' => true]);
        }
    }

    // إضافة مرفق للرسالة
    public function addAttachment($filePath, $originalName): void
    {
        $metadata = $this->metadata ?? [];
        $metadata['attachments'] = array_merge(
            $metadata['attachments'] ?? [],
            [
                [
                    'path' => $filePath,
                    'original_name' => $originalName,
                    'uploaded_at' => now()
                ]
            ]
        );
        
        $this->update(['metadata' => $metadata]);
    }
}