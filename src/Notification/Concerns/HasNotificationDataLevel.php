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
     * @param  NotificationLevelEnum  $level
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
    public function info(?string $message = null, ?int $timeout = null): static
    {
        $this->level = NotificationLevelEnum::Info;
        $this->message = $message;
        $this->timeout = $timeout ?? $this->timeout;
        $this->icon['level'] = 'info';

        return $this;
    }

    /**
     * Shortcut for message & level for Info
     */
    public function success(?string $message = null, ?int $timeout = null): static
    {
        $this->level = NotificationLevelEnum::Success;
        $this->message = $message;
        $this->timeout = $timeout ?? $this->timeout;
        $this->icon['level'] = 'success';

        return $this;
    }

    /**
     * Shortcut for message & level for Warning
     */
    public function warning(?string $message = null, ?int $timeout = null): static
    {
        $this->level = NotificationLevelEnum::Warning;
        $this->message = $message;
        $this->timeout = $timeout ?? $this->timeout;
        $this->icon['level'] = 'warning';

        return $this;
    }

    /**
     * Shortcut for message & level for Error
     */
    public function error(?string $message = null, ?int $timeout = null): static
    {
        $this->level = NotificationLevelEnum::Error;
        $this->message = $message ?? $this->message;
        $this->timeout = $timeout ?? $this->timeout;
        $this->icon['level'] = 'danger';

        return $this;
    }
}
