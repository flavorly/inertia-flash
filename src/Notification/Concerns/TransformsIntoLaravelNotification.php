<?php

namespace Flavorly\InertiaFlash\Notification\Concerns;

use Flavorly\InertiaFlash\Notification\Contracts\InertiaFlashNotification;

trait TransformsIntoLaravelNotification
{
    public function toNotification(): ?InertiaFlashNotification
    {
        /** @var InertiaFlashNotification|null $notification */
        $notification = app(config('inertia-flash.notifications.base_notification'));
        if(!class_exists($notification) || !is_subclass_of($notification, InertiaFlashNotification::class)) {
            return null;
        }

        return new $notification($this);
    }
}
