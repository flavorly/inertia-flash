<?php

namespace Flavorly\InertiaFlash\Notification\Data;

use Flavorly\InertiaFlash\Notification\Enums\NotificationIconColor;
use Flavorly\InertiaFlash\Notification\Enums\NotificationIconType;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;

class NotificationIconData extends Data
{
    public function __construct(
        public string $content,
        public NotificationIconType $type = NotificationIconType::FromLevel,
        public NotificationIconColor $color = NotificationIconColor::Blue,
        /**
         * @var Collection<string,mixed>|null
         */
        public ?Collection $props = null,
    ) {
    }
}
