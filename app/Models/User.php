<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
        'phone',
        'address',
        'profile_photo',
        'specialization',
    ];

    protected $hidden = ['password', 'remember_token'];

    // علاقات
    public function courses(): HasMany
    {
        return $this->hasMany(Course::class, 'instructor_id');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class, 'student_id');
    }

    public function enrolledCourses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'enrollments', 'student_id', 'course_id')
                    ->withTimestamps();
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class, 'student_id');
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

   
    public function studentConversations(): HasMany
    {
        return $this->hasMany(Conversation::class, 'student_id');
    }

    // المحادثات التي يكون فيها المستخدم مديراً
    public function adminConversations(): HasMany
    {
        return $this->hasMany(Conversation::class, 'admin_id');
    }

    // جميع محادثات المستخدم
    public function conversations()
    {
        if ($this->role === 'student') {
            return $this->studentConversations();
        } else {
            return $this->adminConversations();
        }
    }

    // الرسائل المرسلة
    public function sentMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    // الحصول على عدد الرسائل غير المقروءة
    public function getUnreadMessagesCountAttribute(): int
    {
        $conversationIds = $this->conversations()->pluck('id');
        
        if ($conversationIds->isEmpty()) {
            return 0;
        }
        
        return Message::whereIn('conversation_id', $conversationIds)
            ->where('sender_id', '!=', $this->id)
            ->where('is_read', false)
            ->count();
    }

    // الحصول على المديرين المتاحين للمحادثة (للطلاب)
    public function getAvailableAdmins()
    {
        return self::whereIn('role', ['admin', 'instructor'])
            ->where('is_active', true)
            ->where('id', '!=', $this->id)
            ->select('id', 'name', 'email', 'role', 'profile_photo', 'specialization')
            ->get();
    }

    // دوال مساعدة إضافية
    public function canStartConversationWith(User $user): bool
    {
        if ($this->role === 'student') {
            return in_array($user->role, ['admin', 'instructor']) && $user->is_active;
        }
        
        return $user->role === 'student' && $user->is_active;
    }

    public function canAccessConversation(Conversation $conversation): bool
    {
        return $this->id === $conversation->student_id || 
               $this->id === $conversation->admin_id;
    }

    // الحصول على الاسم مع الصورة (للعرض في الواجهة)
    public function getDisplayNameAttribute(): string
    {
        return $this->name . ($this->specialization ? " ({$this->specialization})" : '');
    }

    // التحقق إذا كان المستخدم مديراً أو محاضراً
    public function getIsStaffAttribute(): bool
    {
        return in_array($this->role, ['admin', 'instructor']);
    }
}