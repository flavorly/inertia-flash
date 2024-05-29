<?php

namespace Flavorly\InertiaFlash\Notification\Concerns;

use Flavorly\InertiaFlash\Notification\Enums\NotificationTypeEnum;

trait HasNotificationDataType
{
    /**
     * How the frontend should handle the notification, default is Flash
     */
    public NotificationTypeEnum $kind = NotificationTypeEnum::Flash;

    /**
     * Sets the notification type
     */
    public function type(NotificationTypeEnum $kind): static
    {
        $this->kind = $kind;

        return $this;
    }

    /**
     * Sets the notification type to flash notification
     */
    public function flash(): static
    {
        $this->kind = NotificationTypeEnum::Flash;

        return $this;
    }

    /**
     * Sets the notification type to dialog notification
     */
    public function dialog(): static
    {
        $this->kind = NotificationTypeEnum::Dialog;

        return $this;
    }

    /**
     * Sets the notification type to toast notification
     */
    public function toast(): static
    {
        $this->kind = NotificationTypeEnum::Toast;

        return $this;
    }
}
