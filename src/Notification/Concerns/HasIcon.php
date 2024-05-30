<?php

namespace Flavorly\InertiaFlash\Notification\Concerns;

use Flavorly\InertiaFlash\Notification\Data\NotificationIconData;
use Flavorly\InertiaFlash\Notification\Enums\NotificationIconColor;
use Flavorly\InertiaFlash\Notification\Enums\NotificationIconType;

trait HasIcon
{
    /**
     * Stores the icon configuration
     * If we should get the icon from level, raw icon and props
     */
    public ?NotificationIconData $icon = null;

    /**
     * Raw icon ( usually for emojis or html )
     * Keep in mind that this is not sanitized
     *
     * @param  array<string,mixed>  $props
     */
    public function icon(
        string $content,
        NotificationIconColor $color = NotificationIconColor::Blue,
        array $props = []
    ): static {

        $this->icon = $this->icon ?? new NotificationIconData(
            content: $content,
            type: NotificationIconType::Raw,
            color: $color,
            props: $props,
        );

        return $this;
    }
}
