<?php

namespace Flavorly\InertiaFlash\Notification\Data;

use Spatie\LaravelData\Data;

class NotificationReadableData extends Data
{
    public function __construct(
        /**
         * If the notification is readable
         */
        public bool $enable = false,

        /**
         * The URL to mark the notification as read
         */
        public ?string $url = null,

        /**
         * The URL to mark the notification as read
         */
        public ?string $route = null,

        /**
         * Method to use to mark the notification as read
         */
        public string $method = 'post',
    ) {}
}
