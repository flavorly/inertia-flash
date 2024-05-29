<?php

namespace Flavorly\InertiaFlash\Notification\Data;

use Flavorly\InertiaFlash\Notification\Concerns\HasProps;

class NotificationActionData
{
    use HasProps;

    public function __construct(
        /**
         * The label of the action ( Unique )
         *
         * @var string
         */
        public string $label,

        /**
         * The full URL to the action
         *
         * @var string
         */
        public ?string $url = null,

        /**
         * If the link should open in a new tab
         *
         * @var bool
         */
        public bool $open_in_new_tab = false,

        /**
         * If we should use the frontend router
         * to perform the request
         *
         * @var bool
         */
        public bool $use_router = true,

        /**
         * If the action should close the notification
         *
         * @var bool
         */
        public bool $is_close = false,
    ) {
    }
}
