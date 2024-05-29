<?php

namespace Flavorly\InertiaFlash\Notification\Enums;

enum ContentBlockTypeEnum: string
{
    case Tag = 'tag';
    case Image = 'Image';
    case IFrame = 'iframe';
    case Text = 'text';
    case Html = 'html';
    case UnsafeHtml = 'unsafe_html';
    case Video = 'video';
    case Audio = 'audio';
    case File = 'file';
    case Link = 'link';
    case Button = 'button';
    case Icon = 'icon';
    case Title = 'title';
}
