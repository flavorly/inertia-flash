<?php

namespace Flavorly\InertiaFlash\Notification\Concerns;

trait HasProps
{
    /**
     * Stores the icon configuration
     * If we should get the icon from level, raw icon and props
     *
     * @var array<string,mixed>
     */
    public array $props = [];

    /**
     * Raw icon ( usually for emojis or html )
     * Keep in mind that this is not sanitized
     *
     * @param  array<string,mixed>  $props
     */
    public function props(array $props = []): static
    {
        $this->props = array_merge($this->props, $props);

        return $this;
    }
}
