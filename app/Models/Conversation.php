<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conversation extends Model
{
    protected $fillable = [
        'student_id',
        'admin_id', 
        'title',
        'is_closed',
        'last_message_at'
    ];

    protected $casts = [
        'is_closed' => 'boolean',
        'last_message_at' => 'datetime'
    ];

    // العلاقة مع الطالب
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    // العلاقة مع المدير
    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    // الرسائل في المحادثة
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    // آخر رسالة في المحادثة
    public function lastMessage()
    {
        return $this->hasOne(Message::class)->latest();
    }

    // الحصول على الطرف الآخر في المحادثة
    public function getOtherUserAttribute()
    {
        $currentUserId = auth()->id();
        
        if ($this->student_id === $currentUserId) {
            return $this->admin;
        }
        
        return $this->student;
    }

    // التحقق من وجود رسائل غير مقروءة
    public function getUnreadMessagesCountAttribute()
    {
        return $this->messages()
            ->where('sender_id', '!=', auth()->id())
            ->where('is_read', false)
            ->count();
    }

    // تحديث وقت آخر رسالة
    public function updateLastMessageTime()
    {
        $this->update([
            'last_message_at' => now()
        ]);
    }

    // فتح المحادثة
    public function markAsOpen()
    {
        $this->update(['is_closed' => false]);
    }

    // إغلاق المحادثة
    public function markAsClosed()
    {
        $this->update(['is_closed' => true]);
    }
}