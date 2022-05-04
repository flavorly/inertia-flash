<?php

namespace Igerslike\InertiaFlash\Exceptions;

class InertiaFlashException extends \Exception
{
    public function __construct($driver)
    {
        parent::__construct("Driver '{$driver}' does not support Sharing to user");
    }
}
