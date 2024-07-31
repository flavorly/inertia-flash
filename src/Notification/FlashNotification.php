<?php

namespace Flavorly\InertiaFlash\Notification;

use Flavorly\InertiaFlash\Notification\Concerns\HasContentBlocks;
use Flavorly\InertiaFlash\Notification\Concerns\HasIcon;
use Flavorly\InertiaFlash\Notification\Concerns\HasNotificationActions;
use Flavorly\InertiaFlash\Notification\Concerns\HasNotificationDataKind;
use Flavorly\InertiaFlash\Notification\Concerns\HasNotificationDataLevel;
use Flavorly\InertiaFlash\Notification\Concerns\HasNotificationDataViaChannel;
use Flavorly\InertiaFlash\Notification\Concerns\HasNotificationDispatcher;
use Flavorly\InertiaFlash\Notification\Concerns\HasReadableNotifications;
use Flavorly\InertiaFlash\Notification\Concerns\TransformsIntoLaravelNotification;
use Flavorly\InertiaFlash\Notification\Data\NotificationActionData;
use Flavorly\InertiaFlash\Notification\Data\NotificationIconData;
use Flavorly\InertiaFlash\Notification\Data\NotificationReadableData;
use Flavorly\InertiaFlash\Notification\Data\NotificationTimestampsData;
use Flavorly\InertiaFlash\Notification\Enums\NotificationKindEnum;
use Flavorly\InertiaFlash\Notification\Enums\NotificationLevelEnum;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Spatie\LaravelData\Data;

class FlashNotification extends Data
{
    use HasContentBlocks;
    use HasIcon;
    use HasNotificationActions;
    use HasNotificationDataKind;
    use HasNotificationDataLevel;
    use HasNotificationDataViaChannel;
    use HasNotificationDispatcher;
    use HasReadableNotifications;
    use TransformsIntoLaravelNotification;

    /**
     * A unique ID, if its a persistent notification this should be the ID of the notification on the database
     * otherwise is a uuid generated by the body ( message )
     */
    public int|string $id;

    /**
     * The message to be displayed, required
     */
    public string $message;

    /**
     * Default value to true, if false the notification will not be shown
     */
    public bool $shown = true;

    /**
     * Optional Title for the Notification
     */
    public ?string $title = null;

    /**
     * If we should allow raw HTML on the message to be passed in
     *
     * Default is false
     */
    public bool $allows_unsafe_html = false;

    /**
     * A optional timeout for the notification to be closed
     */
    public ?int $timeout = null;

    /**
     * Stores the timestamps for the notification
     */
    public NotificationTimestampsData $timestamps;

    /**
     * Just a blind constructor, we should be able to compose via fluent
     * So no need to pass any arguments
     */
    public function __construct()
    {
        // But here we need to ensure the defaults
        $this->ensureDefaults();
        $this->actions = collect();
        $this->contentBlocks = collect();
        $this->id = 'n-'.Str::uuid();
        $this->readable = new NotificationReadableData;
        $this->notifiable = null;
        $this->timestamps = new NotificationTimestampsData;
    }

    /**
     * Ensures the user defaults are set
     */
    protected function ensureDefaults(): void
    {
        // @phpstan-ignore-next-line
        $this->via(config('inertia-flash.notifications.defaults.via', []));
        // @phpstan-ignore-next-line
        $this->level(config('inertia-flash.notifications.defaults.level', NotificationLevelEnum::Info));
        // @phpstan-ignore-next-line
        $this->kind(config('inertia-flash.notifications.defaults.kind', NotificationKindEnum::Flash));
    }

    /**
     * The unique ID of the notification
     *
     * @return $this
     */
    public function id(int|string $id): static
    {
        $this->id = $id;

        // Generate the URL once a new ID is set
        $this->ensureReadableURLIsGenerated();

        return $this;
    }

    /**
     * Sets a message & title for the notification
     */
    public function message(string $message, ?string $title = null): static
    {
        $this->message = $message;
        $this->title = $title ?? $this->title;

        return $this;
    }

    /**
     * Sets the title for the notification
     */
    public function title(?string $title = null): static
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Allows the notification to have raw HTML
     */
    public function safe(): static
    {
        $this->allows_unsafe_html = false;

        return $this;
    }

    /**
     * Disallows the notification to have raw HTML
     */
    public function unsafe(): static
    {
        $this->allows_unsafe_html = true;

        return $this;
    }

    /**
     * Defines the timeout for the notification
     */
    public function timeout(?int $timeout = null): static
    {
        // @phpstan-ignore-next-line
        $this->timeout = $timeout ?? intval(config('inertia-flash.notifications.defaults.timeout', 5000));

        return $this;
    }

    /**
     * Returns the notification as a json
     */
    public function __toString(): string
    {
        $encode = json_encode($this->toArray());
        if ($encode === false) {
            return '';
        }

        return $encode;
    }

    /**
     * Attempts to create a Notification from the Database Record
     */
    public static function fromModel(DatabaseNotification $notification): self
    {
        $data = new self;
        $data->message($notification->data->get('message') ?? '');
        $data->title($notification->data->get('title') ?? null);
        $data->to($notification->notifiable);
        $data->shown = (bool) $notification->data->get('shown', true);
        $data->allows_unsafe_html = (bool) $notification->data->get('allows_unsafe_html', false);
        $data->timeout = $notification->data->get('timeout', null);
        $data->contentBlocks = NotificationContentBlock::collect($notification->data->get('content_blocks', []), Collection::class);
        $data->actions = NotificationActionData::collect($notification->data->get('actions', []), Collection::class);
        $data->icon = NotificationIconData::from($notification->data->get('icon', []));
        $data->level = NotificationLevelEnum::tryFrom($notification->data->get('level', NotificationLevelEnum::Info)) ?? NotificationLevelEnum::Info;
        $data->kind = NotificationKindEnum::tryFrom($notification->data->get('kind', NotificationKindEnum::Flash)) ?? NotificationKindEnum::Flash;
        $data->via = collect($notification->data->get('via', []));
        $data->readable();

        // Timestamps
        $data->timestamps->created_at = $notification->created_at;
        $data->timestamps->read_at = $notification->read_at;
        $data->timestamps->updated_at = $notification->updated_at;
        $data->timestamps->deleted_at = $notification->deleted_at;

        if ($notification->id !== null) {
            $data->id($notification->id);
        }

        return $data;
    }

    /**
     * Attempts to create a Notification from the Database Records
     *
     * @param  mixed|Collection<int,DatabaseNotification>  $notifications
     * @return Collection<int,static>
     */
    public static function fromModelCollection(mixed $notifications): Collection
    {
        // @phpstan-ignore-next-line
        return collect($notifications)->map(fn (DatabaseNotification $notification) => static::fromModel($notification));
    }
}
