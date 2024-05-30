<?php

namespace Flavorly\InertiaFlash\Notification\Actions;

use Exception;
use Flavorly\InertiaFlash\Notification\Contracts\ReadableNotifications;
use Flavorly\InertiaFlash\Notification\Notification;
use Illuminate\Notifications\HasDatabaseNotifications;

class NotificationReadAction implements ReadableNotifications
{
    /**
     * @inheritdoc
     */
    public function read(object $notifiable, Notification $notification): bool
    {
        $uses = class_uses($notifiable);
        if (! in_array(HasDatabaseNotifications::class, $uses === false ? [] : $uses)) {
            return false;
        }

        if(!method_exists($notifiable, 'notifications')) {
            return false;
        }

        try {
            /**
             * @var HasDatabaseNotifications $notifiable
             */
            // @phpstan-ignore-next-line
            $databaseNotification = $notifiable
                ->notifications()
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
    public function getUrl(object $notifiable, Notification $notification): ?string
    {
        if(! $notification->readable) {
            return null;
        }

        if(! $notification->readable->enable || ! $notification->readable->route) {
            return null;
        }

        return route(
            $notification->readable->route,
            [
                'notification' => $notification->id
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function getMethod(object $notifiable, Notification $notification): string
    {
        return $notification->readable->method ?? 'GET';
    }
}
