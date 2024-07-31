<?php

namespace Flavorly\InertiaFlash\Notification\Enums;

enum NotificationKindEnum: string
{
    case Toast = 'toast';
    case Flash = 'flash';
    case Dialog = 'dialog';
}
