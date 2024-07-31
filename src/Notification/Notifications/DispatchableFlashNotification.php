<?php

namespace Flavorly\InertiaFlash\Notification\Notifications;

use Flavorly\InertiaFlash\Notification\Contracts\DispatchableFlashNotificationContract;
use Flavorly\InertiaFlash\Notification\Enums\NotificationViaEnum;
use Flavorly\InertiaFlash\Notification\FlashNotification;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification as BaseNotification;

class DispatchableFlashNotification extends BaseNotification implements DispatchableFlashNotificationContract, ShouldQueue
{
    use InteractsWithSockets;
    use Queueable;

    /**
     * {@inheritdoc}
     */
    public function __construct(public FlashNotification $notification)
    {
    }

    /**
     * {@inheritdoc}
     *
     * @phpstan-return  array<int, string>
     */
    public function via(object $notifiable): array
    {
        // @phpstan-ignore-next-line
        return $this
            ->notification
            ->via
            ->map(fn (string|NotificationViaEnum $via) => $via instanceof NotificationViaEnum ? $via->value : (string) $via)
            ->reject(fn (string $via) => $via === 'inertia')
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
     * {@inheritdoc}
     */
    public function toArray(object $notifiable): array
    {
        $id = $this->id;
        // URL is re-generated here with the actual database ID if present
        if ($this->id) {
            $this->notification->id($id);
        }

        return [
            ...$this->notification->toArray(),
            'id' => $id,
            'created_at' => now()->toDateTimeString(),
            // This a quick workaround, so we can instantly mark this notification as read on a Observer
            'mark_as_read' => $this->notification->via->contains(NotificationViaEnum::Inertia),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function toDatabase(object $notifiable): array
    {
        return $this->toArray($notifiable);
    }

    /**
     * {@inheritdoc}
     */
    public function viaConnections(): array
    {
        return config('inertia-flash.notifications.connections', []);
    }

    /**
     * {@inheritdoc}
     */
    public function viaQueues(): array
    {
        return config('inertia-flash.notifications.queues', []);
    }

    public function broadcastAs(): string
    {
        return 'flash-notification';
    }

    /**
     * Get the notification's database type.
     */
    public function databaseType(object $notifiable): string
    {
        return 'flash-notification';
    }
}
