<?php

namespace Flavorly\InertiaFlash\Notification;

use Flavorly\InertiaFlash\Notification\Concerns\HasNotificationActions;
use Flavorly\InertiaFlash\Notification\Concerns\HasNotificationDataLevel;
use Flavorly\InertiaFlash\Notification\Concerns\HasNotificationDataType;
use Flavorly\InertiaFlash\Notification\Concerns\HasNotificationDataViaChannel;
use Flavorly\InertiaFlash\Notification\Concerns\HasIcon;
use Flavorly\InertiaFlash\Notification\Data\NotificationReadableData;
use Flavorly\InertiaFlash\Notification\Data\NotificationTimestampsData;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;

class Notification extends Data
{
    use HasNotificationActions;
    use HasNotificationDataLevel;
    use HasNotificationDataType;
    use HasNotificationDataViaChannel;
    use HasIcon;

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
     * Stores content blocks if required
     *
     * @var Collection<int, NotificationContentBlock>|null
     */
    public ?Collection $content;

    /**
     * If the notification is readable ( belongs to a backend kind of )
     */
    public ?NotificationReadableData $readable = null;

    /**
     * Stores the timestamps for the notification
     */
    public NotificationTimestampsData $timestamps;

    public function __construct()
    {
    }
}
