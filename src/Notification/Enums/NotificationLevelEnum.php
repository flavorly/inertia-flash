<?php

namespace Flavorly\InertiaFlash\Notification\Enums;

enum NotificationLevelEnum: string
{
    case Success = 'success';
    case Info = 'info';
    case Warning = 'warning';
    case Error = 'error';
    case Blank = 'blank';
}
