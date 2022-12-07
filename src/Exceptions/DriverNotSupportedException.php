<?php

namespace flavorly\InertiaFlash\Exceptions;

class DriverNotSupportedException extends InertiaFlashException
{
    public function __construct($driver)
    {
        parent::__construct("Driver '{$driver}' is not supported on Inertia Flash.");
    }
}
