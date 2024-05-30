<?php

namespace Flavorly\InertiaFlash\Notification\Concerns;

use Flavorly\InertiaFlash\Notification\Enums\NotificationTypeEnum;

trait HasNotificationDataType
{
    /**
     * How the frontend should handle the notification, default is Flash
     */
    public NotificationTypeEnum $type = NotificationTypeEnum::Flash;

    /**
     * Sets the notification type
     */
    public function type(NotificationTypeEnum $kind): static
    {
        $this->type = $kind;

        return $this;
    }

    /**
     * Sets the notification type to flash notification
     */
    public function flash(): static
    {
        $this->type = NotificationTypeEnum::Flash;

        return $this;
    }

    /**
     * Sets the notification type to dialog notification
     */
    public function dialog(): static
    {
        $this->type = NotificationTypeEnum::Dialog;

        return $this;
    }

    /**
     * Sets the notification type to toast notification
     */
    public function toast(): static
    {
        $this->type = NotificationTypeEnum::Toast;

        return $this;
    }
}
