<?php

namespace Flavorly\InertiaFlash\Notification\Actions;

use Exception;
use Flavorly\InertiaFlash\Notification\Contracts\ReadsFlashNotifications;
use Flavorly\InertiaFlash\Notification\Notification;
use Illuminate\Notifications\HasDatabaseNotifications;

class NotificationReadAction implements ReadsFlashNotifications
{
    /**
     * @inheritdoc
     */
    public function read(mixed $notifiable, Notification $notification): bool
    {
        if (! in_array(HasDatabaseNotifications::class, class_uses($notifiable))) {
            return false;
        }

        try {
            /**
             * @var HasDatabaseNotifications $notifiable
             */
            $databaseNotification = $notifiable
                ?->notifications()
                ->where('id', $notification->id)
                ->first();

            // No method to read the notification or the notification does not exist
            if (! $databaseNotification || !method_exists($databaseNotification, 'markAsRead')) {
                return false;
            }

            $databaseNotification->markAsRead();

            return true;
        } catch (Exception $e) {
            report($e);

            return false;
        }
    }

    /**
     * @inheritdoc
     */
    public function getUrl(mixed $notifiable, Notification $notification): ?string
    {
        if(! $notification->readable->enable || ! $notification->readable->route || ! $notification->readable->data) {
            return null;
        }

        return route(
            $notification->readable->route,
            $notification->readable->data
        );
    }

    /**
     * @inheritdoc
     */
    public function getMethod(mixed $notifiable, Notification $notification): string
    {
        return $notification->readable->method ?? 'GET';
    }
}
