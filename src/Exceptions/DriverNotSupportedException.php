<?php

namespace Flavorly\InertiaFlash\Exceptions;

class DriverNotSupportedException extends InertiaFlashException
{
    public function __construct(string $driver)
    {
        parent::__construct("Driver '$driver' is not supported on Inertia Flash.");
    }
}
