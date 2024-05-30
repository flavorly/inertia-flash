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
     * @var mixed|HasDatabaseNotifications $notifiable
     * @var Notification $notification
     * @return bool
     */
    public function read(mixed $notifiable, Notification $notification): bool;


    /**
     * Get the URL to mark the notification as read
     *
     *
     * @var mixed|HasDatabaseNotifications $notifiable
     * @var Notification $notification
     * @return string|null
     */
    public function getUrl(mixed $notifiable, Notification $notification): ?string;

    /**
     * Get the method to mark the notification as read
     *
     * @var Notification $notification
     * @var mixed|HasDatabaseNotifications $notifiable
     * @return string
     */
    public function getMethod(mixed $notifiable, Notification $notification): string;
}
