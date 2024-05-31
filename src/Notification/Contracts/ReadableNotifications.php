<?php

namespace Flavorly\InertiaFlash\Notification\Contracts;

use Flavorly\InertiaFlash\Notification\FlashNotification;

interface ReadableNotifications
{
    /**
     * Reads a Flash Notification
     * Receives a "user" or a notifiable object and a notification
     * Returns a boolean indicating if the notification was read
     */
    public function read(object $notifiable, FlashNotification $notification): bool;

    /**
     * Get the URL to mark the notification as read
     */
    public function getUrl(object $notifiable, FlashNotification $notification): ?string;

    /**
     * Get the method to mark the notification as read
     */
    public function getMethod(object $notifiable, FlashNotification $notification): string;
}
