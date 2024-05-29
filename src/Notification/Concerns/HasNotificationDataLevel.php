<?php

namespace Flavorly\InertiaFlash\Notification\Concerns;

use Flavorly\InertiaFlash\Notification\Enums\NotificationLevelEnum;

trait HasNotificationDataLevel
{
    /**
     * Level of the notification, default is Info
     */
    public NotificationLevelEnum $level = NotificationLevelEnum::Info;

    /**
     * Set the notification to given level
     *
     * @return $this
     */
    public function level(NotificationLevelEnum $level): static
    {
        $this->level = $level;

        return $this;
    }

    /**
     * Shortcut for message & level for Info
     */
    public function info(): static
    {
        $this->level = NotificationLevelEnum::Info;

        return $this;
    }

    /**
     * Shortcut for message & level for Info
     */
    public function success(): static
    {
        $this->level = NotificationLevelEnum::Success;

        return $this;
    }

    /**
     * Shortcut for message & level for Warning
     */
    public function warning(): static
    {
        $this->level = NotificationLevelEnum::Warning;

        return $this;
    }

    /**
     * Shortcut for message & level for Error
     */
    public function error(): static
    {
        $this->level = NotificationLevelEnum::Error;

        return $this;
    }
}
