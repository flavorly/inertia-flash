<?php

namespace Flavorly\InertiaFlash\Notification\Enums;

enum NotificationTypeEnum: string
{
    case Toast = 'toast';
    case Flash = 'flash';
    case Dialog = 'dialog';
}
