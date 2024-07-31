<?php

namespace Flavorly\InertiaFlash\Notification\Data;

use Flavorly\InertiaFlash\Notification\Enums\NotificationIconColor;
use Spatie\LaravelData\Data;

class NotificationIconData extends Data
{
    public function __construct(
        /**
         * The content of the icon/emoji
         */
        public ?string $content = null,

        /**
         * The color of the icon
         *
         * @var NotificationIconColor
         */
        public ?NotificationIconColor $color = null,

        /**
         * @var array<string,mixed>|null
         */
        public ?array $props = null,
    ) {}
}
