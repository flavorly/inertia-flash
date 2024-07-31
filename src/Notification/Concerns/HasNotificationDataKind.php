<?php

namespace Flavorly\InertiaFlash\Notification\Concerns;

use Flavorly\InertiaFlash\Notification\Enums\NotificationKindEnum;

trait HasNotificationDataKind
{
    /**
     * How the frontend should handle the notification, default is Flash
     */
    public NotificationKindEnum $kind = NotificationKindEnum::Flash;

    /**
     * Sets the notification type
     */
    public function kind(NotificationKindEnum $kind): static
    {
        $this->kind = $kind;

        return $this;
    }

    /**
     * Sets the notification type to flash notification
     */
    public function flash(): static
    {
        $this->kind = NotificationKindEnum::Flash;

        return $this;
    }

    /**
     * Sets the notification type to dialog notification
     */
    public function dialog(): static
    {
        $this->kind = NotificationKindEnum::Dialog;

        return $this;
    }

    /**
     * Sets the notification type to toast notification
     */
    public function toast(): static
    {
        $this->kind = NotificationKindEnum::Toast;

        return $this;
    }
}
