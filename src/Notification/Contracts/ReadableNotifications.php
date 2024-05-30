<?php

namespace Flavorly\InertiaFlash\Notification\Contracts;

use Flavorly\InertiaFlash\Notification\Notification;
use Illuminate\Notifications\HasDatabaseNotifications;

interface ReadableNotifications
{
    /**
     * Reads a Flash Notification
     * Receives a "user" or a notifiable object and a notification
     * Returns a boolean indicating if the notification was read
     *
     *
     * @param object $notifiable
     * @param Notification $notification
     * @return bool
     */
    public function read(object $notifiable, Notification $notification): bool;


    /**
     * Get the URL to mark the notification as read
     *
     *
     * @param  object $notifiable
     * @param  Notification  $notification
     *
     * @return string|null
     */
    public function getUrl(object $notifiable, Notification $notification): ?string;

    /**
     * Get the method to mark the notification as read
     *
     * @param  object  $notifiable
     * @param  Notification  $notification
     * @return string
     */
    public function getMethod(object $notifiable, Notification $notification): string;
}
