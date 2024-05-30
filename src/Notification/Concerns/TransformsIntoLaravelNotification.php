<?php

namespace Flavorly\InertiaFlash\Notification\Concerns;

use Flavorly\InertiaFlash\Notification\Contracts\NotificationDispatchable;

trait TransformsIntoLaravelNotification
{
    public function toNotification(): ?NotificationDispatchable
    {
        /** @var string|null $notification */
        $notification = config('inertia-flash.notifications.base_notification');
        if ($notification === null) {
            return null;
        }

        if (! class_exists($notification) || ! is_subclass_of($notification, NotificationDispatchable::class)) {
            return null;
        }

        return new $notification($this);
    }
}
