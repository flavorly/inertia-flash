<?php

namespace Flavorly\InertiaFlash\Notification\Data;

use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Spatie\LaravelData\Data;

class NotificationTimestampsData extends Data
{
    public function __construct(
        public null|Carbon|CarbonImmutable $created_at = null,
        public null|Carbon|CarbonImmutable $updated_at = null,
        public null|Carbon|CarbonImmutable $deleted_at = null,
        public null|Carbon|CarbonImmutable $read_at = null,
    ) {
    }
}
