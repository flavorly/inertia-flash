<?php

namespace Flavorly\InertiaFlash\Notification\Concerns;

use Flavorly\InertiaFlash\Notification\Enums\NotificationViaEnum;

trait HasNotificationDispatcher
{
    /**
     * Dispatch the notification
     * Here is where we do the main thing
     */
    public function dispatch(): void
    {
        $this->dispatchViaInertia();
    }

    /**
     * Dispatch via Inertia if required
     */
    protected function dispatchViaInertia(): void
    {
        if ($this->via->contains(NotificationViaEnum::Inertia) || $this->via->contains('inertia')) {
            inertia_flash()->append(
                $this->viaInertiaNamespace,
                $this->toArray()
            );
        }
    }

    protected function dispatchViaBroadcast(): void
    {
        if ($this->via->contains(NotificationViaEnum::Broadcast) || $this->via->contains('broadcast')) {
            // Dispatch via broadcast
        }
    }

    protected function dispatchViaDatabase(): void
    {
        if ($this->via->contains(NotificationViaEnum::Database) || $this->via->contains('database')) {
            // Dispatch via database
        }
    }
}
