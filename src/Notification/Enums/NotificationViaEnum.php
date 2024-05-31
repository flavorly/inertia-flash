<?php

namespace Flavorly\InertiaFlash\Notification\Enums;

enum NotificationViaEnum: string
{
    case Database = 'database';
    case Inertia = 'inertia';
    case Mail = 'mail';
    case Broadcast = 'broadcast';
}
