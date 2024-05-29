<?php

namespace Flavorly\InertiaFlash\Notification\Notifications;

use Flavorly\InertiaFlash\Notification\Contracts\InertiaFlashNotification;
use Flavorly\InertiaFlash\Notification\Contracts\ReadsFlashNotifications;
use Flavorly\InertiaFlash\Notification\Enums\NotificationViaEnum;
use Flavorly\InertiaFlash\Notification\Notification;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification as BaseNotification;

class AbstractNotificationInertia extends BaseNotification implements InertiaFlashNotification, ShouldQueue
{
    use InteractsWithSockets;
    use Queueable;

    /**
     * {@inheritdoc}
     */
    public function __construct(protected Notification $notification)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function via(object $notifiable): array
    {
        return $this
            ->notification
            ->via
            ->map(fn (string|NotificationViaEnum $via) => $via instanceof NotificationViaEnum ? $via->value : $via)
            ->toArray();
    }

    /**
     * {@inheritdoc}
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return (new BroadcastMessage($this->toArray($notifiable)))
            ->onQueue(config('inertia-flash.notifications.queues.broadcast'));
    }

    /**
     * {@inheritdoc}
     */
    public function toMail(object $notifiable): MailMessage
    {
        $message = new MailMessage;
        $message->subject($this->notification?->title ?? '');
        $message->line('');
        $message->line($this->notification->message);
        $message->line('');
        if (! empty($this->notification->actions)) {
            foreach ($this->notification->actions as $action) {
                $message->action(
                    $action->label,
                    $action->url
                );
            }
        }
        $message->line('');
        $message->line(trans('inertia-flash::translations.email-footer', ['app_name' => config('app.name')]));

        return $message;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $hasDatabase = $this->notification->via->contains(NotificationViaEnum::Database);
        // If its Database channel, then we try to be smart here and append the necessary stuff
        if ($hasDatabase) {
            /** @var ReadsFlashNotifications $readable */
            $readable = app(config('inertia-flash.notifications.readable'));
            $this->notification->readable->enable = true;
            $this->notification->readable->url = $readable->getUrl($notifiable, $this->notification);
            $this->notification->readable->method = $readable->getMethod($notifiable, $this->notification);
        }

        return [
            ...$this->notification->toArray(),
            'id' => $this->id,
            'created_at' => now()->toDateTimeString(),
            'read_at' => null,
        ];
    }

    /**
     * Routes the notification to database
     */
    public function toDatabase(object $notifiable): array
    {
        return $this->toArray($notifiable);
    }

    /**
     * Determine which connections should be used for each notification channel.
     *
     * @return array<string, string>
     */
    public function viaConnections(): array
    {
        return config('inertia-flash.notifications.connections');
    }

    /**
     * Determine which queues should be used for each notification channel.
     *
     * @return array<string, string>
     */
    public function viaQueues(): array
    {
        return config('inertia-flash.notifications.queues');
    }
}
