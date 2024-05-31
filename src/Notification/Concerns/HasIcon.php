<?php

namespace Flavorly\InertiaFlash\Notification\Concerns;

use Flavorly\InertiaFlash\Notification\Data\NotificationIconData;
use Flavorly\InertiaFlash\Notification\Enums\NotificationIconColor;

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
        ?NotificationIconColor $color = null,
        array $props = []
    ): static {

        if($this->icon !== null){
            $this->icon->color = $color;
            $this->icon->content = $content;
            $this->icon->props = $props;
            return $this;
        }

        $this->icon = new NotificationIconData(
            content: $content,
            color: $color,
            props: $props,
        );


        return $this;
    }
}
