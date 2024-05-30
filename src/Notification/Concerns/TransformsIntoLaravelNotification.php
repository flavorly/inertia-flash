<?php

namespace Flavorly\InertiaFlash\Notification\Concerns;

use Flavorly\InertiaFlash\Notification\Contracts\DispatchableFlashNotificationContract;

trait TransformsIntoLaravelNotification
{
    public function toNotification(): ?DispatchableFlashNotificationContract
    {
        /** @var string|null $notification */
        $notification = config('inertia-flash.notifications.base_notification');
        if ($notification === null) {
            return null;
        }

        if (! class_exists($notification) || ! is_subclass_of($notification, DispatchableFlashNotificationContract::class)) {
            return null;
        }

        return new $notification($this);
    }
}
