<?php

namespace Flavorly\InertiaFlash\Notification\Concerns;

use Flavorly\InertiaFlash\Notification\Contracts\ReadableNotifications;
use Flavorly\InertiaFlash\Notification\Data\NotificationReadableData;

trait HasReadableNotifications
{
    /**
     * If the notification is readable ( belongs to a backend kind of )
     */
    public ?NotificationReadableData $readable = null;

    public function readable(?string $id = null, ?string $route = null): self
    {
        $this->readable = new NotificationReadableData();
        $this->readable->enable = true;
        $this->readable->route = config('inertia-flash.notifications.defaults.read_route');

        if(filled($route)){
            $this->readable->route = $route;
        }

        if(filled($id)){
            $this->id = $id;
        }

        $this->ensureReadableURLIsGenerated();
        return $this;
    }

    /**
     * Attempts to set the URL for the notification based on the readable
     *
     * @return HasReadableNotifications
     */
    public function ensureReadableURLIsGenerated(): static
    {
        if(!$this->notifiable || !$this->readable){
            return $this;
        }

        /** @var ReadableNotifications $readable */
        $readable = app(config('inertia-flash.notifications.readable'));
        $this->readable->url = $readable->getUrl($this->notifiable, $this);
        $this->readable->method = $readable->getMethod($this->notifiable, $this);

        return $this;
    }
}
