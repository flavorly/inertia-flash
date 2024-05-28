<?php

namespace Flavorly\InertiaFlash\Exceptions;

class InertiaFlashException extends \Exception
{
    public function __construct(string $driver)
    {
        parent::__construct("Driver '$driver' does not support Sharing to user");
    }
}
