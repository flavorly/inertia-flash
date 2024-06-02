<?php

use Flavorly\InertiaFlash\InertiaFlash;
use Flavorly\InertiaFlash\Notification\FlashNotification;

if (! function_exists('inertia_flash')) {

    function inertia_flash(): InertiaFlash
    {
        return app(InertiaFlash::class);
    }
}

if (! function_exists('notification')) {
    function notification(): FlashNotification
    {
        return new FlashNotification();
    }
}

if (! function_exists('flash')) {
    function flash(): FlashNotification
    {
        return (new FlashNotification())
            ->viaInertia()
            ->timeout()
            ->success();
    }
}
