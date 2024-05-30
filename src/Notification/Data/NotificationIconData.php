<?php

namespace Flavorly\InertiaFlash\Notification\Data;

use Flavorly\InertiaFlash\Notification\Enums\NotificationIconColor;
use Flavorly\InertiaFlash\Notification\Enums\NotificationIconType;
use Spatie\LaravelData\Data;

class NotificationIconData extends Data
{
    public function __construct(
        /**
         * The content of the icon/emoji
         */
        public ?string $content = null,

        /**
         * The type of the icon, if it should use icon from the level or any one on the content
         *
         * @var NotificationIconType
         */
        public NotificationIconType $type = NotificationIconType::FromLevel,

        /**
         * The color of the icon
         *
         * @var NotificationIconColor
         */
        public NotificationIconColor $color = NotificationIconColor::Blue,

        /**
         * @var array<string,mixed>|null
         */
        public ?array $props = null,
    ) {
    }
}
