<?php

namespace Flavorly\InertiaFlash\Notification\Data;

class NotificationTimestampsData
{
    public function __construct(
        public ?int $created_at = null,
        public ?int $updated_at = null,
        public ?int $deleted_at = null,
        public ?int $read_at = null,
    ) {
    }
}
