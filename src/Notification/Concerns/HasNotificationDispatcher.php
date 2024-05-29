<?php

namespace Flavorly\InertiaFlash\Notification\Concerns;

use Flavorly\InertiaFlash\Notification\Enums\NotificationViaEnum;
use Illuminate\Notifications\RoutesNotifications;

trait HasNotificationDispatcher
{
    /**
     * The user who will receive the notification
     * @var mixed|RoutesNotifications
     */
    protected mixed $notifiable;

    /**
     * Set the notifiable user/model
     *
     * @var mixed|RoutesNotifications $notifiable
     * @return static
     */
    public function to(mixed $notifiable): static
    {
        $this->notifiable = $notifiable;
        return $this;
    }

    /**
     * Dispatch the notification ( Queued if that's the case )
     * Here is where we do the main thing
     */
    public function dispatch(): void
    {
        $this->dispatchViaInertia();
    }

    /**
     * Dispatch the notification now, immediately, similar to Laravel
     * Here is where we do the main thing
     */
    public function dispatchNow(): void
    {
        $this->dispatchViaInertia();
    }

    /**
     * Attempt to dispatch via Laravel
     * @param  bool  $now
     * @return void
     */
    protected function dispatchViaLaravel(bool $now = false): void
    {
        if(! $this->notifiable) {
            return;
        }
        $this->notifiable->notify($this->toNotification());
    }

    /**
     * Dispatch via Inertia if required
     */
    protected function dispatchViaInertia(): void
    {
        // Noop, we dont want to share via Inertia on console
        if(app()->runningInConsole()) {
            return;
        }

        if ($this->via->contains(NotificationViaEnum::Inertia) || $this->via->contains('inertia')) {
            inertia_flash()->append(
                $this->viaInertiaNamespace,
                $this->toArray()
            );
        }
    }

    /**
     * Attempts to get notifiable user/model
     *
     * @return void
     */
    protected function attemptToGetNotifiable(): void
    {
        // Already has one
        if ($this->notifiable) {
            return;
        }

        // In console, jobs, etc
        if(app()->runningInConsole()) {
            return;
        }

        // Likely the user logged in that we want to notify
        $model = auth()->user();
        // Model exists and routes notifications
        if($model && in_array(RoutesNotifications::class, class_uses($model))) {
            $this->notifiable = $model;
        }
    }
}
