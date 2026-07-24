<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

/**
 * UserPortalAccessNotification
 *
 * Sends a real email to the student/teacher with a secure,
 * time-limited, one-click login link to their portal.
 *
 * NOTE: This notification does NOT implement ShouldQueue,
 * so it is dispatched synchronously (immediately) without
 * needing a queue worker running.
 */
class UserPortalAccessNotification extends Notification
{
    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Build the mail notification message.
     *
     * Generates a 48-hour temporary signed URL that lets the
     * student log in with a single click — no password required.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $roleName = ucfirst($notifiable->role ?? 'User');

        // Generate a secure, signed URL valid for 48 hours
        $accessUrl = URL::temporarySignedRoute(
            'portal.access',
            now()->addHours(48),
            ['user' => $notifiable->id]
        );

        return (new MailMessage)
            ->subject('Welcome to Student Management System — Your Portal Access Link')
            ->greeting("Hello {$notifiable->name},")
            ->line("You have been registered in the **Student Management System** as a **{$roleName}**.")
            ->line('Click the button below to log in directly to your portal — no password required:')
            ->action('🎓 Access My Portal Now', $accessUrl)
            ->line('This link is valid for **48 hours** for security purposes.')
            ->line('If you did not expect this email, please contact the school administration.')
            ->salutation('Regards, Student Management System');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [];
    }
}
