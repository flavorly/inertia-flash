<?php

namespace Flavorly\InertiaFlash\Exceptions;

class PrimaryKeyNotFoundException extends InertiaFlashException
{
    public function __construct()
    {
        parent::__construct("A primary key is required to share with flash. Please set the primary key on your model.");
    }
}
