<?php

namespace Flavorly\InertiaFlash\Notification\Concerns;

use Flavorly\InertiaFlash\Notification\Data\NotificationActionData;
use Illuminate\Support\Collection;

trait HasNotificationActions
{
    /**
     * A collection of Actions to be displayed on the notification
     *
     * @var Collection<int, NotificationActionData>
     */
    public Collection $actions;

    /**
     * Adds an action to the notification
     *
     * @param  array<string,mixed>  $props
     */
    public function action(string|NotificationActionData $labelOrAction, string $url, array $props = [], bool $newTab = false): static
    {
        // If the label is already an instance of NotificationActionData
        if ($labelOrAction instanceof NotificationActionData) {
            $this->actions->push($labelOrAction);

            return $this;
        }

        // Add Only if action label is not the same
        if ($this->actions->some(fn (NotificationActionData $action) => $action->label === $labelOrAction)) {
            return $this;
        }

        // Add the action
        $this->actions->push(new NotificationActionData(
            label: $labelOrAction,
            url: $url,
            open_in_new_tab: $newTab,
            is_close: false,
            props: $props
        ));

        return $this;
    }

    /**
     * Special close action to trigger close the notification / dialog
     *
     * @param  array<string,mixed>  $props
     */
    public function closable(?string $label = null, array $props = []): static
    {
        $this->actions->push(new NotificationActionData(
            label: $label ?? trans('app.generic.close'),
            is_close: true,
            props: $props
        ));

        return $this;
    }
}
