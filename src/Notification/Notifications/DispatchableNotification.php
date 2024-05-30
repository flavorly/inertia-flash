<?php

namespace Flavorly\InertiaFlash\Notification\Notifications;

use Flavorly\InertiaFlash\Notification\Contracts\NotificationDispatchable;
use Flavorly\InertiaFlash\Notification\Contracts\ReadableNotifications;
use Flavorly\InertiaFlash\Notification\Enums\NotificationViaEnum;
use Flavorly\InertiaFlash\Notification\Notification;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification as BaseNotification;

class DispatchableNotification extends BaseNotification implements NotificationDispatchable, ShouldQueue
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
     * @phpstan-return  array<int, string>
     */
    public function via(object $notifiable): array
    {
        // @phpstan-ignore-next-line
        return $this
            ->notification
            ->via
            ->map(fn (string|NotificationViaEnum $via) => $via instanceof NotificationViaEnum ? $via->value : (string) $via)
            ->toArray();
    }

    /**
     * {@inheritdoc}
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return (new BroadcastMessage($this->toArray($notifiable)))
            // @phpstan-ignore-next-line
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
                    $action->url ?? '#'
                );
            }
        }
        $message->line('');
        // @phpstan-ignore-next-line
        $message->line(trans('inertia-flash::translations.email-footer', ['app_name' => config('app.name')]));

        return $message;
    }

    /**
     * @inheritdoc
     */
    public function toArray(object $notifiable): array
    {
        $id = $this->id;
        // URL is re-generated here with the actual database ID if present
        $this->notification->id($id);
        return [
            ...$this->notification->toArray(),
            'id' => $id,
            'created_at' => now()->toDateTimeString(),
            'read_at' => null,
        ];
    }

    /**
     * @inheritdoc
     */
    public function toDatabase(object $notifiable): array
    {
        return $this->toArray($notifiable);
    }

    /**
     * @inheritdoc
     */
    public function viaConnections(): array
    {
        return config('inertia-flash.notifications.connections', []);
    }

    /**
     * @inheritdoc
     */
    public function viaQueues(): array
    {
        return config('inertia-flash.notifications.queues', []);
    }
}
