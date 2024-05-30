<?php

namespace Flavorly\InertiaFlash\Notification\Concerns;

use Flavorly\InertiaFlash\Notification\Enums\NotificationViaEnum;
use Illuminate\Notifications\RoutesNotifications;

trait HasNotificationDispatcher
{
    /**
     * The user who will receive the notification
     */
    protected ?object $notifiable;

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
        $this->dispatchViaInertia();
        $this->dispatchViaLaravel();

        return $this;
    }

    /**
     * Dispatch the notification now, immediately, similar to Laravel
     * Here is where we do the main thing
     */
    public function dispatchNow(): static
    {
        $this->dispatchViaInertia();
        $this->dispatchViaLaravel(true);

        return $this;
    }

    /**
     * Attempt to dispatch via Laravel
     */
    protected function dispatchViaLaravel(bool $now = false): void
    {
        if (! $this->notifiable) {
            return;
        }

        if (
            ! method_exists($this->notifiable, 'notifyNow') ||
            ! method_exists($this->notifiable, 'notify')
        ) {
            return;
        }

        if ($now) {
            // @phpstan-ignore-next-line
            $this->notifiable->notifyNow($this->toNotification());

            return;
        }
        // @phpstan-ignore-next-line
        $this->notifiable->notify($this->toNotification());
    }

    /**
     * Dispatch via Inertia if required
     */
    protected function dispatchViaInertia(): void
    {
        // Noop, we dont want to share via Inertia on console
        if ($this->via->contains(NotificationViaEnum::Inertia) || $this->via->contains('inertia')) {
            inertia_flash()->append(
                $this->viaInertiaNamespace,
                $this->toArray()
            );
        }
    }

    /**
     * Attempts to get notifiable user/model
     */
    protected function attemptToGetNotifiable(): void
    {
        // Already has one
        if ($this->notifiable) {
            return;
        }

        // In console, jobs, etc
        if (app()->runningInConsole()) {
            return;
        }

        // Likely the user logged in that we want to notify
        $model = auth()->user();
        // Model exists and routes notifications
        if ($model && in_array(RoutesNotifications::class, class_uses($model))) {
            $this->notifiable = $model;
        }
    }
}
