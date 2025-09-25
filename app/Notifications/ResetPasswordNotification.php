<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    public $token; 

    /**
     * Create a new notification instance.
     */
    public function __construct($token)
    {
        $this->token = $token; 
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
   public function toMail($notifiable)
{
    // الرابط الخاص بالـ frontend (صفحة Vue)
    $frontendUrl = config('app.frontend_url'); // مثال: http://127.0.0.1:5173
    $resetUrl = $frontendUrl . '/reset-password?token=' . $this->token . '&email=' . urlencode($notifiable->email);

    return (new MailMessage)
        ->subject('Reset Password')
        ->line('You are receiving this email because we received a password reset request for your account.')
        ->action('Reset Password', $resetUrl)
        ->line('If you did not request a password reset, no further action is required.');
}

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [];
    }
}
