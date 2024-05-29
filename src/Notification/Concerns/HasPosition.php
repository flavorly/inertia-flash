<?php

namespace Flavorly\InertiaFlash\Notification\Concerns;

trait HasPosition
{
    /**
     * Stores the icon configuration
     * If we should get the icon from level, raw icon and props
     */
    public int $position = 1;

    /**
     * Raw icon ( usually for emojis or html )
     * Keep in mind that this is not sanitized
     *
     * @param  int  $position
     * @return $this
     */
    public function position(int $position): static
    {
        $this->position = $position;
        return $this;
    }
}
