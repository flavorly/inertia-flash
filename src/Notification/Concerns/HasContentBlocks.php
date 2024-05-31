<?php

namespace Flavorly\InertiaFlash\Notification\Concerns;

use Closure;
use Flavorly\InertiaFlash\Notification\NotificationContentBlock;
use Illuminate\Support\Collection;

trait HasContentBlocks
{
    /**
     * Stores the icon configuration
     * If we should get the icon from level, raw icon and props
     *
     * @var Collection<int,NotificationContentBlock>|null
     */
    public ?Collection $contentBlocks = null;

    /**
     * Appends a content block to the notification
     *
     * Only usefully for Dialogs / Toasts
     * Because for flash notifications you can just use the message
     *
     * @param  NotificationContentBlock|Closure(NotificationContentBlock):NotificationContentBlock  $content
     */
    public function block(NotificationContentBlock|Closure $content): static
    {
        if ($content instanceof Closure) {
            $content = $content(new NotificationContentBlock());
        }
        $this->contentBlocks = $this->contentBlocks ?? collect();
        $this->contentBlocks->push($content);

        return $this;
    }
}
