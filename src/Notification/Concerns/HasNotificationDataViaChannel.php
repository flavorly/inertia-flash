<?php

namespace Flavorly\InertiaFlash\Notification\Concerns;

use Flavorly\InertiaFlash\Notification\Enums\NotificationViaEnum;
use Illuminate\Support\Collection;

trait HasNotificationDataViaChannel
{
    /**
     * How the notification should be sent
     * Via Inertia, Email, sms, etc
     *
     * @var Collection<int,(string|NotificationViaEnum)>
     */
    public Collection $via;

    /**
     * Routes the notification to the given channel, defaults to Broadcast & Database
     *
     * @param  array<int,(string|NotificationViaEnum)>  $via
     */
    public function via(array $via = [
        NotificationViaEnum::Broadcast,
        NotificationViaEnum::Database,
    ]): static
    {
        $this->via = collect($via);

        return $this;
    }

    /**
     * Sends Via Broadcast
     */
    public function viaBroadcast(): static
    {
        if (! $this->via->contains(NotificationViaEnum::Broadcast)) {
            $this->via->push(NotificationViaEnum::Broadcast);
        }

        return $this;
    }

    /**
     * Sends Via Database
     */
    public function viaDatabase(): static
    {
        if (! $this->via->contains(NotificationViaEnum::Database)) {
            $this->via->push(NotificationViaEnum::Database);
        }

        return $this;
    }

    /**
     * Sends Via Mail
     */
    public function viaMail(): static
    {
        if (! $this->via->contains(NotificationViaEnum::Mail)) {
            $this->via->push(NotificationViaEnum::Mail);
        }

        return $this;
    }

    /**
     * Sends Via Mail
     */
    public function viaInertia(): static
    {
        if (! $this->via->contains(NotificationViaEnum::Inertia)) {
            $this->via->push(NotificationViaEnum::Inertia);
        }

        return $this;
    }
}
