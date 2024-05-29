<?php

use Flavorly\InertiaFlash\InertiaFlash;
use Flavorly\InertiaFlash\Notification\Notification;

if (! function_exists('inertia_flash')) {

    function inertia_flash(): InertiaFlash
    {
        return app(InertiaFlash::class);
    }
}

if (! function_exists('flash')) {
    function flash(): Notification
    {
        return app(Notification::class);
    }
}
