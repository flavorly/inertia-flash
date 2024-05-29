<?php

namespace Flavorly\InertiaFlash\Notification\Data;

use Illuminate\Support\Collection;
use Modules\Notifications\Enums\NotificationIconColor;
use Modules\Notifications\Enums\NotificationIconType;
use Spatie\LaravelData\Data;

class NotificationIconData extends Data
{
    public function __construct(
        public string $content,
        public NotificationIconType $type = NotificationIconType::FromLevel,
        public NotificationIconColor $color = NotificationIconColor::Blue,
        public ?Collection $props = null,
    ) {
    }
}
