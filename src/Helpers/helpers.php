<?php

use Flavorly\InertiaFlash\InertiaFlash;
use Flavorly\InertiaFlash\Notification\Notification;

if (! function_exists('inertia_flash')) {

    function inertia_flash(): InertiaFlash
    {
        return app(InertiaFlash::class);
    }
}

if (! function_exists('notification')) {
    function notification(): Notification
    {
        return new Notification();
    }
}

if (! function_exists('flash')) {
    function flash(): Notification
    {
        return (new Notification())
            ->viaInertia()
            ->success();
    }
}
