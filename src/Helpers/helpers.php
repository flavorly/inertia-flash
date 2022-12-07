<?php

use Flavorly\InertiaFlash\InertiaFlash;

if(!function_exists('inertia_flash')){

    function inertia_flash(): InertiaFlash
    {
        return app(InertiaFlash::class);
    }
}
