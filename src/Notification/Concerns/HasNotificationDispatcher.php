<?php

namespace Flavorly\InertiaFlash\Notification\Concerns;

use Flavorly\InertiaFlash\Notification\Enums\NotificationViaEnum;
use Illuminate\Notifications\Notifiable;

trait HasNotificationDispatcher
{
    /**
     * The user who will receive the notification
     */
    protected ?object $notifiable = null;

    /**
     * Set the notifiable user/model
     */
    public function to(object $notifiable): static
    {
        $this->notifiable = $notifiable;

        return $this;
    }

    /**
     * @return $this
     */
    public function toUser(): static
    {
        $this->attemptToGetNotifiable();

        return $this;
    }

    /**
     * Dispatch the notification ( Queued if that's the case )
     * Here is where we do the main thing
     */
    public function dispatch(): static
    {
        $this->ensureIdIsUniqueBeforeDispatch();
        $this->dispatchViaLaravel();
        $this->dispatchViaInertia();

        return $this;
    }

    /**
     * Dispatch the notification now, immediately, similar to Laravel
     * Here is where we do the main thing
     */
    public function dispatchNow(): static
    {
        $this->ensureIdIsUniqueBeforeDispatch();
        $this->dispatchViaLaravel(true);
        $this->dispatchViaInertia();

        return $this;
    }

    /**
     * Attempt to dispatch via Laravel
     */
    protected function dispatchViaLaravel(bool $now = false): void
    {
        $this->attemptToGetNotifiable();

        if (! $this->notifiable) {
            return;
        }

        if (
            ! method_exists($this->notifiable, 'notifyNow') ||
            ! method_exists($this->notifiable, 'notify')
        ) {
            return;
        }

        $toNotification = $this->toNotification();

        if ($now) {
            // @phpstan-ignore-next-line
            $this->notifiable->notifyNow($toNotification);

            return;
        }

        // @phpstan-ignore-next-line
        $this->notifiable->notify($toNotification);
    }

    /**
     * Dispatch via Inertia if required
     */
    protected function dispatchViaInertia(): void
    {
        // Noop, we dont want to share via Inertia on console
        if ($this->via->contains(NotificationViaEnum::Inertia) || $this->via->contains('inertia')) {
            inertia_flash()->append(
                // @phpstan-ignore-next-line
                $this->viaInertiaNamespace ?? config('inertia-flash.notifications.defaults.namespace', 'flashNotifications'),
                $this->toArray()
            );
        }
    }

    /**
     * Ensure we are able to generate a unique ID
     */
    protected function ensureIdIsUniqueBeforeDispatch(): void
    {
        if (! $this->id) {
            return;
        }

        // @phpstan-ignore-next-line
        if (str_starts_with($this->id, 'n-')) {
            $this->id = md5($this->message.$this->title.$this->id);
        }
    }

    /**
     * Attempts to get notifiable user/model
     */
    protected function attemptToGetNotifiable(): void
    {
        // Already has one
        if (filled($this->notifiable)) {
            return;
        }

        // Likely the user logged in that we want to notify
        $model = auth()->user();

        // Model exists and routes notifications
        if ($model && in_array(Notifiable::class, class_uses($model))) {
            $this->notifiable = $model;
        }
    }
}
