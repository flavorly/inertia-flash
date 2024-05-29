<?php

namespace Flavorly\InertiaFlash\Notification\Concerns;

use Flavorly\InertiaFlash\Notification\Data\NotificationReadableData;

trait HasReadableNotifications
{
    /**
     * If the notification is readable ( belongs to a backend kind of )
     */
    public ?NotificationReadableData $readable = null;
}
