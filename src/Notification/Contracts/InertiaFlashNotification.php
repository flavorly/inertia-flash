<?php

namespace Flavorly\InertiaFlash\Notification\Contracts;

use Flavorly\InertiaFlash\Notification\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;

interface InertiaFlashNotification
{
    /**
     * Create a new notification instance.
     */
    public function __construct(Notification $notification);

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array;

    /**
     * Get the broadcast representation of the notification.
     */
    public function toBroadcast(object $notifiable): BroadcastMessage;

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage;
}
